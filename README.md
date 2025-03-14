# Dockerized Nginx Reverse Proxy with Token Validation

## Overview
This project provides a **Dockerized Nginx reverse proxy** that validates an authentication token using a PHP script (`index.php`). The proxy is designed to handle requests efficiently while enforcing access control based on predefined rules.

## Features
- **Dockerized Deployment**: Easily deployable via Docker Compose.
- **Nginx Reverse Proxy**: Routes incoming traffic and applies access restrictions.
- **Token Validation**: Authentication via a PHP backend (`index.php`).
- **Device and Origin Filtering**: Uses `map` directives in `nginx.conf` to allow or deny requests based on user agent, referer, and custom headers.
- **Support for PHP-FPM**: Enables fast execution of PHP scripts via FastCGI.
- **Logging and Error Handling**: Implements structured logging to track access and authentication failures.

## Architecture
The system consists of:
1. **Nginx** - Serves as a reverse proxy, filtering requests based on headers and forwarding them to PHP.
2. **PHP-FPM** - Processes authentication requests and validates tokens.
3. **Docker Compose** - Manages containerized deployment.

## Installation & Setup
### Prerequisites
Ensure you have the following installed:
- **Docker** (>= 20.x)
- **Docker Compose** (>= v2.x)

### Steps to Deploy
1. **Clone the repository**
   ```sh
   git clone https://github.com/santiagovasquez/nginx-token-proxy.git
   cd nginx-token-proxy
   ```
2. **Build and Start the Services**
   ```sh
   docker-compose up --build
   ```
3. **Verify the Deployment**
   ```sh
   curl -H "X-User: testuser" -H "X-Verify-Web: 1" http://localhost:8081/
   ```

## Configuration
### Nginx (`nginx.conf`)
- **Device & Origin Validation**: Filters based on `User-Agent`, `Referer`, and custom headers.
- **PHP Processing**: Forwards `.php` requests to PHP-FPM.
- **Access Control**: Blocks unauthorized requests.

### Docker Compose (`docker-compose.yml`)
- Defines a service for **Nginx + PHP-FPM**.
- Mounts the required configurations.
- Uses a custom **Docker network** for internal communication.

### PHP Authentication (`index.php`)
- Extracts **X-User** and **Token** from headers.
- Validates tokens based on a time-based mechanism.
- Logs unauthorized attempts.

## Security Considerations
- **Use HTTPS**: Deploy behind a TLS-enabled proxy.
- **Restrict IP Access**: Modify Nginx rules to allow only trusted IPs.
- **Rate Limiting**: Implement request limits in Nginx.

## Author
SANTIAGO VASQUEZ OLARTE.
