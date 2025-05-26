-- Inserção de dados iniciais para o Marketplace Automotivo

-- Inserção de marcas populares
INSERT INTO brands (name) VALUES 
('Honda'),
('Toyota'),
('Volkswagen'),
('Chevrolet'),
('Fiat'),
('Ford'),
('Hyundai'),
('Jeep'),
('Nissan'),
('Renault');

-- Inserção de modelos para cada marca
-- Honda
INSERT INTO models (brand_id, name) VALUES 
(1, 'Civic'),
(1, 'Fit'),
(1, 'HR-V'),
(1, 'City'),
(1, 'CR-V');

-- Toyota
INSERT INTO models (brand_id, name) VALUES 
(2, 'Corolla'),
(2, 'Hilux'),
(2, 'Yaris'),
(2, 'SW4'),
(2, 'RAV4');

-- Volkswagen
INSERT INTO models (brand_id, name) VALUES 
(3, 'Gol'),
(3, 'Polo'),
(3, 'T-Cross'),
(3, 'Virtus'),
(3, 'Nivus');

-- Chevrolet
INSERT INTO models (brand_id, name) VALUES 
(4, 'Onix'),
(4, 'Tracker'),
(4, 'Cruze'),
(4, 'S10'),
(4, 'Spin');

-- Fiat
INSERT INTO models (brand_id, name) VALUES 
(5, 'Uno'),
(5, 'Argo'),
(5, 'Mobi'),
(5, 'Toro'),
(5, 'Strada');

-- Ford
INSERT INTO models (brand_id, name) VALUES 
(6, 'Ka'),
(6, 'EcoSport'),
(6, 'Ranger'),
(6, 'Mustang'),
(6, 'Territory');

-- Hyundai
INSERT INTO models (brand_id, name) VALUES 
(7, 'HB20'),
(7, 'Creta'),
(7, 'Tucson'),
(7, 'i30'),
(7, 'Santa Fe');

-- Jeep
INSERT INTO models (brand_id, name) VALUES 
(8, 'Renegade'),
(8, 'Compass'),
(8, 'Commander'),
(8, 'Wrangler'),
(8, 'Cherokee');

-- Nissan
INSERT INTO models (brand_id, name) VALUES 
(9, 'Versa'),
(9, 'Kicks'),
(9, 'Sentra'),
(9, 'Frontier'),
(9, 'March');

-- Renault
INSERT INTO models (brand_id, name) VALUES 
(10, 'Kwid'),
(10, 'Sandero'),
(10, 'Logan'),
(10, 'Duster'),
(10, 'Captur');

-- Usuário administrador padrão
INSERT INTO users (name, email, password, user_type, status) VALUES 
('Administrador', 'admin@automarket.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active');

-- Usuários de exemplo
INSERT INTO users (name, email, password, phone, user_type, city, state) VALUES 
('João Silva', 'joao@exemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '(11) 98765-4321', 'seller', 'São Paulo', 'SP'),
('Maria Oliveira', 'maria@exemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '(21) 98765-4321', 'buyer', 'Rio de Janeiro', 'RJ'),
('Carlos Santos', 'carlos@exemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '(31) 98765-4321', 'seller', 'Belo Horizonte', 'MG');

-- Veículos de exemplo
INSERT INTO vehicles (brand_id, model_id, year_manufacture, year_model, mileage, transmission, fuel_type, color, doors, engine, power) VALUES 
(1, 1, 2020, 2020, 45000, 'automatic', 'flex', 'Preto', 4, '2.0', '155 cv'),
(2, 6, 2021, 2021, 32000, 'automatic', 'flex', 'Prata', 4, '2.0', '177 cv'),
(3, 13, 2022, 2022, 18000, 'automatic', 'flex', 'Branco', 4, '1.4 TSI', '150 cv'),
(4, 16, 2019, 2020, 52000, 'manual', 'flex', 'Vermelho', 4, '1.0', '116 cv'),
(5, 21, 2021, 2022, 28000, 'automatic', 'diesel', 'Cinza', 4, '2.0', '170 cv'),
(6, 28, 2018, 2018, 65000, 'manual', 'flex', 'Azul', 4, '1.5', '137 cv'),
(7, 31, 2022, 2023, 12000, 'automatic', 'flex', 'Prata', 4, '1.0 Turbo', '120 cv'),
(8, 36, 2021, 2021, 35000, 'automatic', 'flex', 'Verde', 4, '2.0', '166 cv');

-- Anúncios de exemplo
INSERT INTO listings (user_id, vehicle_id, title, description, price, location, status, featured, verified) VALUES 
(2, 1, 'Honda Civic EXL 2.0 2020', 'Honda Civic EXL 2.0 em excelente estado. Único dono, todas as revisões feitas na concessionária, IPVA 2025 pago. Veículo com baixa quilometragem e muito bem conservado.', 119900.00, 'São Paulo - SP', 'active', 1, 1),
(2, 2, 'Toyota Corolla XEI 2.0 2021', 'Toyota Corolla XEI 2.0 em perfeito estado. Carro de único dono, com todas as revisões em dia. Baixa quilometragem, IPVA 2025 pago.', 129900.00, 'Rio de Janeiro - RJ', 'active', 1, 1),
(2, 3, 'Volkswagen T-Cross Highline 2022', 'Volkswagen T-Cross Highline 1.4 TSI em estado de zero. Apenas 18 mil km rodados, todas as revisões na concessionária, IPVA 2025 pago.', 139900.00, 'Belo Horizonte - MG', 'active', 0, 1),
(4, 4, 'Chevrolet Onix LTZ 1.0 Turbo 2020', 'Chevrolet Onix LTZ 1.0 Turbo em ótimo estado. Segundo dono, todas as revisões em dia, IPVA 2025 pago. Aceito troca por veículo de menor valor.', 79900.00, 'Belo Horizonte - MG', 'active', 0, 0),
(4, 5, 'Fiat Toro Ranch 2.0 Diesel 2022', 'Fiat Toro Ranch 2.0 Diesel 4x4 AT9 em excelente estado. Único dono, baixa quilometragem, todas as revisões na concessionária. Veículo para pessoas exigentes.', 159900.00, 'São Paulo - SP', 'active', 1, 1),
(2, 6, 'Ford EcoSport SE 1.5 2018', 'Ford EcoSport SE 1.5 em bom estado. Segundo dono, documentação em dia, pneus novos. Aceito financiamento.', 69900.00, 'Rio de Janeiro - RJ', 'active', 0, 0),
(4, 7, 'Hyundai Creta Platinum 1.0 Turbo 2023', 'Hyundai Creta Platinum 1.0 Turbo GDI em estado de zero. Apenas 12 mil km rodados, na garantia de fábrica, IPVA 2025 pago.', 149900.00, 'Curitiba - PR', 'active', 1, 1),
(2, 8, 'Jeep Compass Limited 2.0 Flex 2021', 'Jeep Compass Limited 2.0 Flex em excelente estado. Único dono, todas as revisões na concessionária, IPVA 2025 pago.', 145900.00, 'Brasília - DF', 'active', 1, 0);

-- Fotos de exemplo (caminhos fictícios)
INSERT INTO photos (listing_id, file_name, file_path, order_position, is_primary) VALUES 
(1, 'civic_1.jpg', '/uploads/vehicles/civic_1.jpg', 1, 1),
(1, 'civic_2.jpg', '/uploads/vehicles/civic_2.jpg', 2, 0),
(1, 'civic_3.jpg', '/uploads/vehicles/civic_3.jpg', 3, 0),
(2, 'corolla_1.jpg', '/uploads/vehicles/corolla_1.jpg', 1, 1),
(2, 'corolla_2.jpg', '/uploads/vehicles/corolla_2.jpg', 2, 0),
(2, 'corolla_3.jpg', '/uploads/vehicles/corolla_3.jpg', 3, 0),
(3, 'tcross_1.jpg', '/uploads/vehicles/tcross_1.jpg', 1, 1),
(3, 'tcross_2.jpg', '/uploads/vehicles/tcross_2.jpg', 2, 0),
(3, 'tcross_3.jpg', '/uploads/vehicles/tcross_3.jpg', 3, 0),
(4, 'onix_1.jpg', '/uploads/vehicles/onix_1.jpg', 1, 1),
(4, 'onix_2.jpg', '/uploads/vehicles/onix_2.jpg', 2, 0),
(5, 'toro_1.jpg', '/uploads/vehicles/toro_1.jpg', 1, 1),
(5, 'toro_2.jpg', '/uploads/vehicles/toro_2.jpg', 2, 0),
(6, 'ecosport_1.jpg', '/uploads/vehicles/ecosport_1.jpg', 1, 1),
(7, 'creta_1.jpg', '/uploads/vehicles/creta_1.jpg', 1, 1),
(7, 'creta_2.jpg', '/uploads/vehicles/creta_2.jpg', 2, 0),
(8, 'compass_1.jpg', '/uploads/vehicles/compass_1.jpg', 1, 1);

-- Mensagens de exemplo
INSERT INTO messages (listing_id, sender_id, receiver_id, message, read_status) VALUES
(1, 3, 2, 'Olá, este veículo ainda está disponível?', 0),
(1, 2, 3, 'Sim, está disponível. Podemos agendar uma visita?', 0),
(2, 3, 2, 'Olá, aceita financiamento?', 0),
(2, 2, 3, 'Sim, aceitamos financiamento. Qual banco você prefere?', 0);

-- Favoritos de exemplo
INSERT INTO favorites (user_id, listing_id) VALUES
(3, 1),
(3, 5),
(3, 7);
