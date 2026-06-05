import asyncio, os, re, json, logging, pymysql, hashlib
from dotenv import load_dotenv
from telethon import TelegramClient, events
from telethon.tl.types import MessageMediaDocument

load_dotenv()
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s [%(levelname)s] %(message)s',
    handlers=[logging.FileHandler('monitor.log'), logging.StreamHandler()]
)
log = logging.getLogger(__name__)

API_ID   = int(os.getenv('TG_API_ID'))
API_HASH = os.getenv('TG_API_HASH')
PHONE    = os.getenv('TG_PHONE')

DB_CONFIG = dict(
    host=os.getenv('DB_HOST', '127.0.0.1'),
    port=int(os.getenv('DB_PORT', 3306)),
    db=os.getenv('DB_NAME', 'secradar'),
    user=os.getenv('DB_USER', 'secradar'),
    passwd=os.getenv('DB_PASS', 'secret123'),
    charset='utf8mb4'
)

CANAIS = [
    'combolist_leaks',
    'comboleaks',
]

EMAIL_RE = re.compile(r'[\w\.\-\+]+@[\w\-]+\.[\w\.]+')
CPF_RE   = re.compile(r'\d{3}[\.\-]?\d{3}[\.\-]?\d{3}[\-\.]?\d{2}')
PHONE_RE = re.compile(r'(?:\+55|55)?(\d{2})(\d{4,5})(\d{4})')

def get_db():
    return pymysql.connect(**DB_CONFIG)

def create_tables():
    conn = get_db(); cur = conn.cursor()

    # Tabela de matches — só guarda o que interessa aos clientes
    cur.execute('''
        CREATE TABLE IF NOT EXISTS breach_matches (
            id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id     BIGINT UNSIGNED NOT NULL,
            email       VARCHAR(255) NOT NULL,
            password    VARCHAR(500) NULL,
            phone       VARCHAR(30)  NULL,
            cpf         VARCHAR(20)  NULL,
            breach_name VARCHAR(255) NOT NULL,
            severity    ENUM("critical","high","medium","low") DEFAULT "medium",
            notified    TINYINT(1) DEFAULT 0,
            created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user  (user_id),
            INDEX idx_email (email),
            UNIQUE KEY unique_match (user_id, email, breach_name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ''')

    # Tabela de controle de arquivos já processados
    cur.execute('''
        CREATE TABLE IF NOT EXISTS processed_files (
            id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            file_hash  VARCHAR(64) NOT NULL UNIQUE,
            canal      VARCHAR(100),
            matches    INT DEFAULT 0,
            processed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ''')

    conn.commit(); cur.close(); conn.close()
    log.info("✔ Tabelas prontas")

def get_client_emails() -> dict:
    """
    Retorna dicionário {email_normalizado: user_id}
    com todos os e-mails monitorados ativos.
    """
    try:
        conn = get_db(); cur = conn.cursor()
        cur.execute('''
            SELECT me.email, me.user_id
            FROM monitored_emails me
            INNER JOIN subscriptions s ON s.user_id = me.user_id
            WHERE me.status = "active"
            AND s.status = "authorized"
        ''')
        emails = {row[0].lower().strip(): row[1] for row in cur.fetchall()}
        cur.close(); conn.close()
        log.info(f"📋 {len(emails)} e-mails de clientes carregados")
        return emails
    except Exception as e:
        log.error(f"Erro ao carregar e-mails: {e}")
        return {}

def save_match(user_id, email, password, phone, cpf, breach_name):
    """Salva apenas os matches relevantes para clientes."""
    try:
        severity = 'critical' if password else ('high' if cpf or phone else 'medium')
        conn = get_db(); cur = conn.cursor()
        cur.execute('''
            INSERT IGNORE INTO breach_matches
                (user_id, email, password, phone, cpf, breach_name, severity)
            VALUES (%s, %s, %s, %s, %s, %s, %s)
        ''', (user_id, email, password, phone, cpf, breach_name, severity))
        inserted = cur.rowcount
        conn.commit(); cur.close(); conn.close()
        return inserted > 0
    except Exception as e:
        log.error(f"Erro save_match: {e}")
        return False

def already_processed(file_hash: str) -> bool:
    """Verifica se este arquivo já foi processado antes."""
    try:
        conn = get_db(); cur = conn.cursor()
        cur.execute('SELECT id FROM processed_files WHERE file_hash=%s', (file_hash,))
        exists = cur.fetchone() is not None
        cur.close(); conn.close()
        return exists
    except:
        return False

def mark_processed(file_hash: str, canal: str, matches: int):
    try:
        conn = get_db(); cur = conn.cursor()
        cur.execute('INSERT IGNORE INTO processed_files (file_hash, canal, matches) VALUES (%s,%s,%s)',
            (file_hash, canal, matches))
        conn.commit(); cur.close(); conn.close()
    except:
        pass

def parse_line(line: str) -> dict:
    """Extrai email, senha, cpf e telefone de uma linha."""
    line = line.strip()
    if not line or len(line) < 6:
        return {}

    email = password = phone = cpf = None

    # Formato email:senha ou email;senha
    if ':' in line or ';' in line:
        parts = re.split(r'[;:]', line, maxsplit=1)
        if len(parts) == 2 and EMAIL_RE.match(parts[0].strip()):
            email    = parts[0].strip().lower()
            password = parts[1].strip()[:500]

    # Email solto
    if not email:
        m = EMAIL_RE.search(line)
        if m: email = m.group(0).lower()

    # CPF
    m = CPF_RE.search(line)
    if m:
        c = re.sub(r'[\.\-]', '', m.group(0))
        if len(c) == 11: cpf = c

    # Telefone BR
    m = PHONE_RE.search(line)
    if m: phone = f"+55{m.group(1)}{m.group(2)}{m.group(3)}"

    return dict(email=email, password=password, phone=phone, cpf=cpf)

def process_content(content: str, breach_name: str, client_emails: dict) -> int:
    """
    Processa conteúdo linha a linha.
    Salva APENAS os e-mails que pertencem a clientes ativos.
    Não armazena nada além disso.
    """
    matches = 0
    for line in content.splitlines():
        data = parse_line(line)
        if not data.get('email'):
            continue

        # Verifica se é e-mail de algum cliente
        user_id = client_emails.get(data['email'])
        if not user_id:
            continue  # Ignora — não é cliente nosso

        # É cliente! Salva o match
        saved = save_match(
            user_id    = user_id,
            email      = data['email'],
            password   = data.get('password'),
            phone      = data.get('phone'),
            cpf        = data.get('cpf'),
            breach_name= breach_name
        )
        if saved:
            matches += 1
            log.warning(f"🚨 MATCH encontrado: cliente {user_id} — {data['email']} em {breach_name}")

    return matches

async def process_file(client, message, breach_name: str, client_emails: dict) -> int:
    """
    Baixa arquivo → processa → apaga.
    Armazena ZERO dados brutos, apenas matches de clientes.
    """
    tmp_path = f'/tmp/secradar_breach_{message.id}.tmp'
    matches  = 0

    try:
        # Calcula hash antes de baixar para evitar reprocessar
        file_id = str(message.media.document.id)
        file_hash = hashlib.sha256(file_id.encode()).hexdigest()

        if already_processed(file_hash):
            log.info(f"⏭ Arquivo já processado: {file_hash[:16]}...")
            return 0

        log.info(f"⬇ Baixando arquivo de @{breach_name}...")
        path = await client.download_media(message, file=tmp_path)
        if not path:
            return 0

        # Tenta ler com diferentes encodings
        for enc in ['utf-8', 'latin-1', 'cp1252']:
            try:
                with open(path, 'r', encoding=enc, errors='ignore') as f:
                    # Lê em chunks para não estourar memória
                    chunk_size = 50_000  # 50k linhas por vez
                    buffer = []
                    for line in f:
                        buffer.append(line)
                        if len(buffer) >= chunk_size:
                            matches += process_content('\n'.join(buffer), breach_name, client_emails)
                            buffer = []
                    if buffer:
                        matches += process_content('\n'.join(buffer), breach_name, client_emails)
                break
            except Exception:
                continue

        mark_processed(file_hash, breach_name, matches)
        log.info(f"✔ Arquivo processado: {matches} matches de clientes encontrados")

    except Exception as e:
        log.error(f"Erro ao processar arquivo: {e}")
    finally:
        # SEMPRE apaga o arquivo temporário
        if os.path.exists(tmp_path):
            os.remove(tmp_path)
            log.info(f"🗑 Arquivo temporário apagado")

    return matches

async def main():
    create_tables()

    client = TelegramClient('secradar_session', API_ID, API_HASH)
    await client.start(phone=PHONE)
    log.info("✔ Conectado ao Telegram")

    # Carrega e-mails dos clientes
    client_emails = get_client_emails()

    if not client_emails:
        log.warning("⚠ Nenhum cliente ativo ainda. Monitor aguardando clientes se cadastrarem...")

    # Processa histórico recente
    for canal in CANAIS:
        try:
            log.info(f"📡 Verificando histórico: @{canal}")
            async for msg in client.iter_messages(canal, limit=50):
                if not msg: continue
                bn = f"tg_{canal}"

                if msg.text and len(msg.text) > 20:
                    process_content(msg.text, bn, client_emails)

                if msg.media and isinstance(msg.media, MessageMediaDocument):
                    mime = getattr(msg.media.document, 'mime_type', '')
                    if any(t in mime for t in ['text','csv','plain','octet']):
                        await process_file(client, msg, bn, client_emails)

        except Exception as e:
            log.warning(f"Canal @{canal} inacessível: {e}")

    log.info("👁 Monitorando novos vazamentos em tempo real...")
    log.info(f"   Clientes protegidos: {len(client_emails)}")

    @client.on(events.NewMessage(chats=CANAIS))
    async def handler(event):
        # Recarrega e-mails dos clientes a cada novo evento
        emails = get_client_emails()
        canal  = getattr(event.chat, 'username', 'unknown')
        bn     = f"tg_{canal}"
        matches = 0

        if event.text:
            matches += process_content(event.text, bn, emails)

        if event.media and isinstance(event.media, MessageMediaDocument):
            mime = getattr(event.media.document, 'mime_type', '')
            if any(t in mime for t in ['text','csv','plain','octet']):
                matches += await process_file(event.client, event.message, bn, emails)

        if matches > 0:
            log.warning(f"🚨 {matches} clientes afetados por vazamento em @{canal}!")

    await client.run_until_disconnected()

asyncio.run(main())
