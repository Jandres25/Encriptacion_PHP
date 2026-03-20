-- Seed data for the `users` table
-- All passwords hashed with bcrypt (PASSWORD_DEFAULT)
-- Passwords: Admin/Luca/Martins/Gus = their respective passwords; Juan/Sofy/Mary = '0000'

USE `login`;

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `username`, `password`, `is_admin`) VALUES
(1, 'Enrique', 'Gonzalez',    'enrique@gmail.com', 'Admin',   '$2y$10$3T3hu9AM7shPtkFQfM7jluFTMLfL474gUjbfN7eMTcSlPTrb.ktLK', 1),
(2, 'Lucas',        'Martinez Peralta', 'usuario2@gmail.com',   'Luca',    '$2y$10$yZcR40UMne.eQxTVNFb0jOIYrEC6Tj2dj1yJt2FGxzMizNHc0hZWS', 0),
(3, 'Juan Juanito', 'Perez Mamio',      'juanito@gmail.com',    'Juan',    '$2y$10$dfekTN5qnbcGdq5GEKhDgOrOW6Em08wJqdoRSbxW9Ay3yVJthCztK', 0),
(4, 'Sofia',        'Oropesa Cespedez', 'sofia@gmail.com',      'Sofy',    '$2y$10$dfekTN5qnbcGdq5GEKhDgOrOW6Em08wJqdoRSbxW9Ay3yVJthCztK', 0),
(5, 'Maria Christina', 'Johnson Smith', 'maria@gmail.com',      'Mary',    '$2y$10$dfekTN5qnbcGdq5GEKhDgOrOW6Em08wJqdoRSbxW9Ay3yVJthCztK', 0),
(6, 'Martin',       'Morales',          'martin@gmail.com',     'Martins', '$2y$10$x.bQLZ9TfGsOHmIl5lwM8.badBz9G9kqLRlYhxw25Lpzq0HN0wK8m', 0),
(7, 'Gustavo',      'Aguilar Mendoza',  'gustavo@gmail.com',    'Gus',     '$2y$10$3YQR0fPiN4xeMIL3QYDXBOQrOWr6BQfRgwjeFdB1Bf18OlOM6M0NO', 0);
