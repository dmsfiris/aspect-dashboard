/**
 * @fileoverview Main API server for Aspect Dashboard.
 *
 * A clean and modern Node.js API backend.
 *
 * Part of the open-source AspectSoft project collection.
 * © 2025 AspectSoft – MIT License
 * https://github.com/dmsfiris/aspect-dashboard
 */
 
const express = require('express');
const fs = require('fs');
const path = require('path');

const app = express();
const PORT = 5000;

// === File Paths ===
const storesFile = path.join(__dirname, 'stores', 'stores.json');
const domainsFile = path.join(__dirname, 'domains', 'domains.json');

// === Middleware ===
app.use(express.json());

// === Helpers ===
function readJSON(filePath, label) {
  try {
    const data = fs.readFileSync(filePath, 'utf-8');
    console.log(`Reading ${label} from:`, filePath);
    return JSON.parse(data);
  } catch (err) {
    console.error(`Error reading ${label}:`, err.message);
    return [];
  }
}

function writeJSON(filePath, data, label) {
  try {
    fs.writeFileSync(filePath, JSON.stringify(data, null, 2));
    console.log(`Wrote ${label} to:`, filePath);
  } catch (err) {
    console.error(`Error writing ${label}:`, err.message);
  }
}

// =======================
// === STORES ROUTES ====
// =======================

app.get('/api/stores', (req, res) => {
  const stores = readJSON(storesFile, 'stores');
  res.json(stores);
});

app.get('/api/stores/:id', (req, res) => {
  const stores = readJSON(storesFile, 'stores');
  const store = stores.find(s => String(s.id) === req.params.id);
  store ? res.json(store) : res.status(404).json({ message: 'Store not found' });
});

app.post('/api/stores', (req, res) => {
  const stores = readJSON(storesFile, 'stores');

  // Calculate max existing id and increment by 1
  const maxId = stores.reduce((max, s) => Math.max(max, s.id || 0), 0);
  const newId = maxId + 1;

  const newStore = {
    id: newId,
    name: req.body.name,
    owner: req.body.owner,
    contact_email: req.body.contact_email
  };

  stores.push(newStore);
  writeJSON(storesFile, stores, 'stores');
  res.status(201).json(newStore);
});

app.put('/api/stores/:id', (req, res) => {
  const stores = readJSON(storesFile, 'stores');
  const index = stores.findIndex(s => String(s.id) === req.params.id);

  if (index === -1) return res.status(404).json({ message: 'Store not found' });

  stores[index] = { ...stores[index], ...req.body };
  writeJSON(storesFile, stores, 'stores');
  res.json(stores[index]);
});

app.delete('/api/stores/:id', (req, res) => {
  let stores = readJSON(storesFile, 'stores');
  const initial = stores.length;
  stores = stores.filter(s => String(s.id) !== req.params.id);

  if (stores.length === initial) return res.status(404).json({ message: 'Store not found' });

  writeJSON(storesFile, stores, 'stores');
  res.json({ message: 'Store deleted' });
});

// ========================
// === DOMAINS ROUTES ====
// ========================

app.get('/api/domains', (req, res) => {
  const domains = readJSON(domainsFile, 'domains');
  res.json(domains);
});

app.get('/api/domains/:id', (req, res) => {
  const domains = readJSON(domainsFile, 'domains');
  const domain = domains.find(d => String(d.id) === req.params.id);
  domain ? res.json(domain) : res.status(404).json({ message: 'Domain not found' });
});

app.post('/api/domains', (req, res) => {
  const domains = readJSON(domainsFile, 'domains');

  // Calculate max existing id and increment by 1
  const maxId = domains.reduce((max, d) => Math.max(max, d.id || 0), 0);
  const newId = maxId + 1;

  const newDomain = {
    id: newId,
    domain: req.body.domain,
    status: req.body.status,
    store_id: req.body.store_id
  };  

  domains.push(newDomain);
  writeJSON(domainsFile, domains, 'domains');
  res.status(201).json(newDomain);
});

app.put('/api/domains/:id', (req, res) => {
  const domains = readJSON(domainsFile, 'domains');
  const index = domains.findIndex(d => String(d.id) === req.params.id);

  if (index === -1) return res.status(404).json({ message: 'Domain not found' });

  domains[index] = { ...domains[index], ...req.body };
  writeJSON(domainsFile, domains, 'domains');
  res.json(domains[index]);
});

app.delete('/api/domains/:id', (req, res) => {
  let domains = readJSON(domainsFile, 'domains');
  const initial = domains.length;
  domains = domains.filter(d => String(d.id) !== req.params.id);

  if (domains.length === initial) return res.status(404).json({ message: 'Domain not found' });

  writeJSON(domainsFile, domains, 'domains');
  res.json({ message: 'Domain deleted' });
});

// ========================
// === START SERVER ======
// ========================
app.listen(PORT, () => {
  console.log(`API server running at: http://localhost:${PORT}`);
  console.log(`Stores file: ${storesFile}`);
  console.log(`Domains file: ${domainsFile}`);
});
