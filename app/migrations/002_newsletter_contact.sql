ALTER TABLE newsletter_subscribers ADD COLUMN unsubscribe_token TEXT;
ALTER TABLE newsletter_subscribers ADD COLUMN updated_at INTEGER;

CREATE TABLE IF NOT EXISTS contact_messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL,
    subject TEXT,
    message TEXT NOT NULL,
    created_at INTEGER NOT NULL
);
