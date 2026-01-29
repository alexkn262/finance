CREATE TABLE IF NOT EXISTS article_likes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    article_id INTEGER NOT NULL,
    ip_hash TEXT NOT NULL,
    created_at INTEGER NOT NULL,
    UNIQUE(article_id, ip_hash),
    FOREIGN KEY(article_id) REFERENCES articles(id) ON DELETE CASCADE
);
