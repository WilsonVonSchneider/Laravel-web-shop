# Web Shop Example

This is a simple web shop application with an admin panel, built using Laravel.

## Features

- User authentication: Users can sign up, log in, and log out.
- Product browsing: Users can browse available products and view details.
- Orders: Users can add products to their orders.
- Admin panel: Administrators can manage products, categories, and user orders.

## Prerequisites

- Docker
- Docker Compose

## Installation

1. Clone this repository to your local machine: git clone <repository-url>

2. Navigate to the project directory: cd web-shop-example

3. Copy the .env.example file to .env and configure your environment variables: cp .env.example .env

4. Start the Docker containers: docker-compose up -d

5. Generate the application key: php artisan key:generate

6. Migrate the database: php artisan migrate

## Usage

- Access the web shop frontend at [http://localhost:8000](http://localhost:8000/api).
- Access the admin panel at [http://localhost:8000/admin](http://localhost:8000/api/admin).

## Contributing

Contributions are welcome! Please fork this repository and submit a pull request with your changes.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

