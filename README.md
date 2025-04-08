# AI Agent Laravel Application

A Laravel-based application for AI chat agents, allowing dynamic conversations with multiple AI models with client-based API access.

## 📋 Overview

This Laravel application provides an API for interacting with AI models through a chat interface. The system supports multiple clients with their own configurations and default messages, making it adaptable for various use cases.

## ✨ Features

- **Client Management**: Create and manage AI clients with their own tokens and configurations
- **Multi-Model Support**: Currently implements Deepseek models, but architecture supports adding more AI providers
- **Chat Sessions**: Create and manage persistent chat sessions
- **Message History**: Keep track of conversation history
- **Client-specific Default Messages**: Configure default prompts/messages for each client
- **API Documentation**: Comprehensive Swagger documentation

## 🚀 Installation

1. Clone the repository:
```bash
git clone https://github.com/amirmmdz/laravel-ai-agent.git
cd laravel-ai-agent
```

2. Install dependencies:
```bash
composer install
```

3. Copy the environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure your database in the `.env` file:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ai_agent
DB_USERNAME=root
DB_PASSWORD=
```

6. Configure your AI provider credentials:
```
AI_DEEPSEEK_MODEL_NAME=
AI_API_KEY=
AI_BASE_URL=
```

7. Run migrations:
```bash
php artisan migrate
```

8. Generate API documentation:
```bash
php artisan l5-swagger:generate
```

9. Start the server:
```bash
php artisan serve
```

## 📝 API Documentation

Access the API documentation at `/api/documentation` after starting the server.

### Key Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/v1/chats` | Create a new chat session |
| GET | `/api/v1/chats/{uuid}` | Get chat details |
| GET | `/api/v1/chats/{uuid}/messages` | Get all messages for a chat |
| POST | `/api/v1/chats/{uuid}/messages` | Send a new message in a chat |
| GET | `/api/v1/clients` | Get client details |
| GET | `/api/v1/clients/messages` | Get default messages for a client |

## 🔒 Authentication

All API requests require a client token passed in the `X-Client-Token` header. Tokens are associated with AI clients and can be managed through the database or an admin interface.

Example:
```
GET /api/v1/clients
X-Client-Token: your_client_token_here
```

## 🏗️ Architecture

### Models

- **User**: Represents a user who owns AI clients
- **AiClient**: Represents an AI service client with configuration
- **AiClientDefaultMessage**: Default messages/prompts for a client
- **AiChat**: Represents a chat session with an AI model
- **AiChatMessage**: Individual messages within a chat session

### Services

- **AiChatService**: Core service for interacting with AI models
- **DeepseekChatbotHandlerService**: Implementation for Deepseek AI models

### Interfaces

- **IAiChatbotHandler**: Interface for different AI provider implementations

## 🧩 Extending with New AI Providers

To add a new AI provider:

1. Create a new service implementing the `IAiChatbotHandler` interface:
```php
class NewProviderChatbotHandlerService implements IAiChatbotHandler {
    // Implement required methods
}
```

2. Bind the service in the service provider as needed.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/amazing-feature`)
3. Commit your Changes (`git commit -m 'Add some amazing feature'`)
4. Push to the Branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request
