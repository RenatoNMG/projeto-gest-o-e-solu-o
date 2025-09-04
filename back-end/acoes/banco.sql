-- Cria o banco de dados
CREATE DATABASE IF NOT EXISTS gestao;
USE gestao;

-- Tabela de Empresas
CREATE TABLE IF NOT EXISTS empresa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    cnpj VARCHAR(18) UNIQUE NOT NULL,
    token VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- Tabela de Campos (depende da empresa)
CREATE TABLE IF NOT EXISTS campo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    nivel_acesso INT NOT NULL,
    nivel INT DEFAULT 0,
    cor VARCHAR(7),
    id_empresa INT NOT NULL,
    FOREIGN KEY (id_empresa) REFERENCES empresa(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabela de Módulos (depende do campo e da empresa)
CREATE TABLE IF NOT EXISTS modulo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    id_campo INT NOT NULL,
    id_empresa INT NOT NULL,
    FOREIGN KEY (id_campo) REFERENCES campo(id) ON DELETE CASCADE,
    FOREIGN KEY (id_empresa) REFERENCES empresa(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabela de Valores (ligada ao campo e empresa)
CREATE TABLE IF NOT EXISTS valor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    id_campo INT,
    id_empresa INT,
    FOREIGN KEY (id_campo) REFERENCES campo(id) ON DELETE SET NULL,
    FOREIGN KEY (id_empresa) REFERENCES empresa(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabela de Cards
CREATE TABLE IF NOT EXISTS cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    id_modulo INT NOT NULL,
    FOREIGN KEY (id_modulo) REFERENCES modulo(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabela de Dados
CREATE TABLE IF NOT EXISTS dados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    valor VARCHAR(255) NOT NULL,
    id_card INT NOT NULL,
    FOREIGN KEY (id_card) REFERENCES cards(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Tabela de Imagens
CREATE TABLE IF NOT EXISTS imagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    caminho VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_empresa INT NOT NULL,
    FOREIGN KEY (id_empresa) REFERENCES empresa(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabela de Vendas Flexível
CREATE TABLE IF NOT EXISTS vendas_flexivel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_empresa INT,
    dados_venda JSON,
    FOREIGN KEY (id_empresa) REFERENCES empresa(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabela de Submódulos (criar ANTES do item_submodulo)
CREATE TABLE IF NOT EXISTS submodulo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    id_modulo INT NOT NULL,
    FOREIGN KEY (id_modulo) REFERENCES modulo(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabela de Item do Submódulo (depende do submodulo)
CREATE TABLE IF NOT EXISTS item_submodulo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    id_submodulo INT NOT NULL,
    FOREIGN KEY (id_submodulo) REFERENCES submodulo(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabela de Valor do Submódulo
CREATE TABLE IF NOT EXISTS valor_submodulo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    valor INT NOT NULL,
    id_submodulo INT NOT NULL,
    FOREIGN KEY (id_submodulo) REFERENCES submodulo(id) ON DELETE CASCADE
) ENGINE=InnoDB;
