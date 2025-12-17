FROM node:20-alpine

# Set working directory
WORKDIR /app

# Expose port
EXPOSE 8080

# Start the development server
CMD ["sh", "-c", "npm install && npm run dev -- --host 0.0.0.0 --port 8080"]