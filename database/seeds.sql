-- Seed data for the `users` table
-- All passwords hashed with bcrypt (PASSWORD_DEFAULT)
-- Passwords: Admin/Luca/Martins/Gus = '123456'; Juan/Sofy/Mary = '0000'

USE `login`;

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `username`, `password`, `is_admin`) VALUES
(1, 'Enrique',        'Gonzalez',          'enrique@gmail.com',  'Admin',   '$2y$10$MeEI4oAOYxTIk29JUlBgIe.CXpk22zuvq0iOb/v7U5YcwrYG2vRXK', 1),
(2, 'Lucas',          'Martinez Peralta',  'usuario2@gmail.com', 'Luca',    '$2y$10$MeEI4oAOYxTIk29JUlBgIe.CXpk22zuvq0iOb/v7U5YcwrYG2vRXK', 0),
(3, 'Juan Juanito',   'Perez Mamio',       'juanito@gmail.com',  'Juan',    '$2y$10$0DnJQJi6TMSeOYY5bbNokOqL.25cgmQcXRKnZ9x23LQ1vhFAgu4oq', 0),
(4, 'Sofia',          'Oropesa Cespedez',  'sofia@gmail.com',    'Sofy',    '$2y$10$0DnJQJi6TMSeOYY5bbNokOqL.25cgmQcXRKnZ9x23LQ1vhFAgu4oq', 0),
(5, 'Maria Christina','Johnson Smith',     'maria@gmail.com',    'Mary',    '$2y$10$0DnJQJi6TMSeOYY5bbNokOqL.25cgmQcXRKnZ9x23LQ1vhFAgu4oq', 0),
(6, 'Martin',         'Morales',           'martin@gmail.com',   'Martins', '$2y$10$MeEI4oAOYxTIk29JUlBgIe.CXpk22zuvq0iOb/v7U5YcwrYG2vRXK', 0),
(7, 'Gustavo',        'Aguilar Mendoza',   'gustavo@gmail.com',  'Gus',     '$2y$10$MeEI4oAOYxTIk29JUlBgIe.CXpk22zuvq0iOb/v7U5YcwrYG2vRXK', 0);
