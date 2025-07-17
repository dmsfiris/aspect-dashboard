# Domains Data Folder

This folder contains the `domains.json` file, which stores the list of domains associated with stores in the application.

## File Description

- `domains.json`  
  A JSON array where each object represents a domain with the following properties:  
  - `id` (number): Unique identifier for the domain  
  - `domain` (string): Domain name (e.g., `example.com`)  
  - `status` (string): Current status of the domain (e.g., `Active`, `Pending DNS`)  
  - `store_id` (number): The ID of the store this domain is linked to  

## Notes

- This file is read and updated by the Node.js API server (`node-api/index.js`).
- Avoid manual edits while the server is running to prevent data inconsistencies.
- Access to `.json` files may be restricted by `.htaccess` or server configuration for security.
- Backup this file regularly to avoid accidental data loss.

## Usage

The Node.js API exposes CRUD endpoints to manage domains, which read/write this file directly.
