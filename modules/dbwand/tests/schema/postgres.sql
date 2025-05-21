CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price NUMERIC(10, 2) NOT NULL CHECK (price >= 0),
    stock INT NOT NULL DEFAULT 0 CHECK (stock >= 0),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'pending'
);

CREATE TABLE order_items (
    id SERIAL PRIMARY KEY,
    order_id INT NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    product_id INT NOT NULL REFERENCES products(id),
    quantity INT NOT NULL CHECK (quantity > 0),
    price_at_purchase NUMERIC(10, 2) NOT NULL CHECK (price_at_purchase >= 0),
    UNIQUE(order_id, product_id)  -- Un produit ne peut apparaître qu'une fois par commande
);

CREATE TABLE addresses (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    address_line1 VARCHAR(255) NOT NULL,
    address_line2 VARCHAR(255),
    city VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    country VARCHAR(100) NOT NULL
);

ALTER TABLE orders ADD COLUMN shipping_address_id INT REFERENCES addresses(id);


INSERT INTO users (email, username, password_hash)
VALUES
  ('alice@example.com', 'alice', 'hashedpassword1'),
  ('bob@example.com', 'bob', 'hashedpassword2'),
  ('carol@example.com', 'carol', 'hashedpassword3');


INSERT INTO products (name, description, price, stock)
VALUES
  ('Blue T-shirt', '100% cotton T-shirt', 19.99, 50),
  ('Red Cap', 'One-size-fits-all cap', 14.99, 30),
  ('Black Mug', 'Ceramic mug 350ml', 9.99, 100),
  ('Hoodie', 'Unisex fleece hoodie', 39.99, 20);

INSERT INTO addresses (user_id, address_line1, city, postal_code, country)
VALUES
  (1, '12 rue des Lilas', 'Paris', '75010', 'France'),
  (2, '45 avenue du Parc', 'Lyon', '69003', 'France'),
  (3, '89 boulevard Victor', 'Marseille', '13008', 'France');

INSERT INTO orders (user_id, order_date, status, shipping_address_id)
VALUES
  (1, NOW() - INTERVAL '10 days', 'shipped', 1),
  (2, NOW() - INTERVAL '5 days', 'pending', 2),
  (1, NOW() - INTERVAL '2 days', 'delivered', 1);


  -- Commande 1 : 1x T-shirt, 2x Mug
INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase)
VALUES
  (1, 1, 1, 19.99),
  (1, 3, 2, 9.99),
  (2, 2, 1, 14.99),
  (3, 4, 1, 39.99);