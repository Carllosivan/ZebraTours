import http from 'node:http';
import { spawn } from 'node:child_process';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const PORT = Number(process.env.PORT || 3000);
const PHP_PORT = 8085;
let phpProcess: any = null;

// Ensure the PHP 8.2 MVC server is running
function startPhpServer() {
  try {
    // Check if port 8080 is already responding
    const pingReq = http.get(`http://127.0.0.1:${PHP_PORT}/`, (res) => {
      // Already running
      console.log(`[Zebra Tours] Servidor PHP 8.2 ativo no porto ${PHP_PORT}.`);
    });

    pingReq.on('error', () => {
      console.log(`[Zebra Tours] A iniciar servidor PHP 8.2 MVC no porto ${PHP_PORT}...`);
      phpProcess = spawn('php', ['-S', `127.0.0.1:${PHP_PORT}`, '-t', 'public', 'public/index.php'], {
        cwd: __dirname,
        stdio: 'inherit',
      });

      phpProcess.on('exit', (code: number) => {
        console.warn(`[Zebra Tours] Processo PHP terminou (código ${code}). A reiniciar em 2s...`);
        setTimeout(startPhpServer, 2000);
      });
    });
  } catch (err: any) {
    console.error('[Zebra Tours] Erro ao iniciar PHP:', err.message);
  }
}

startPhpServer();

// Clean Proxy Server on port 3000 to forward all traffic directly to PHP MVC
const server = http.createServer((clientReq, clientRes) => {
  const options: http.RequestOptions = {
    hostname: '127.0.0.1',
    port: PHP_PORT,
    path: clientReq.url,
    method: clientReq.method,
    headers: {
      ...clientReq.headers,
      host: clientReq.headers.host || `localhost:${PORT}`,
      'x-forwarded-for': clientReq.socket.remoteAddress || '127.0.0.1',
      'x-forwarded-proto': 'http',
    },
  };

  const proxyReq = http.request(options, (proxyRes) => {
    clientRes.writeHead(proxyRes.statusCode || 200, proxyRes.headers);
    proxyRes.pipe(clientRes, { end: true });
  });

  proxyReq.on('error', (err) => {
    clientRes.writeHead(502, { 'Content-Type': 'text/html; charset=utf-8' });
    clientRes.end(`
      <!DOCTYPE html>
      <html lang="pt">
      <head>
        <meta charset="utf-8">
        <title>Zebra Tours - A Iniciar</title>
        <meta http-equiv="refresh" content="2">
        <script src="https://cdn.tailwindcss.com"></script>
      </head>
      <body class="bg-slate-950 text-white min-h-screen flex items-center justify-center p-6 text-center font-sans">
        <div class="max-w-md w-full bg-slate-900 border border-slate-800 rounded-3xl p-8 space-y-4 shadow-2xl">
          <div class="w-12 h-12 border-4 border-amber-400 border-t-transparent rounded-full animate-spin mx-auto"></div>
          <h1 class="text-lg font-bold text-white">A inicializar o motor PHP MVC...</h1>
          <p class="text-xs text-slate-400">A página será atualizada automaticamente em 2 segundos.</p>
        </div>
      </body>
      </html>
    `);
  });

  if (clientReq.method === 'GET' || clientReq.method === 'HEAD') {
    proxyReq.end();
  } else {
    clientReq.pipe(proxyReq, { end: true });
  }
});

server.listen(PORT, '0.0.0.0', () => {
  console.log(`[Zebra Tours] Servidor operacional na porta ${PORT} -> Encaminhando para PHP 8.2 MVC (${PHP_PORT})`);
});
