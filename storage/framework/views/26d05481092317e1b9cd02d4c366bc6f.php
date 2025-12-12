<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?php echo $__env->yieldContent('title', 'EduSpark'); ?></title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* ⬇️ Place your CSS here — same CSS from your UI (sidebar, cards, etc.) */
        :root{
/* ✅ Your original CSS pasted cleanly */
:root{
    --bg-light:#f5f7ff;
    --bg-dark:#071026;
    --card-light:rgba(255,255,255,0.9);
    --card-dark:#0f1724;
    --accent:#6A4DF7;
    --accent-2:#9C7BFF;
    --muted:#98a0b3;
    --success:#2A9D8F;
    --danger:#E63946;
    --yellow:#F4C430;
}
        body.light{background:var(--bg-light);color:#0b1220;}
*{box-sizing:border-box;}
html,body{height:100%;margin:0;font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,"Helvetica Neue",Arial;}

body.light{background:var(--bg-light);color:#0b1220;}
body.dark{background:var(--bg-dark);color:#e6eef8;}

        .app{display:flex;min-height:100vh;gap:28px;padding:28px;}
        body.light .sidebar{background:rgba(255,255,255,0.9);}
        .sidebar{width:240px;padding:18px;border-radius:16px;display:flex;flex-direction:column;gap:12px;align-items:center;backdrop-filter:blur(8px) saturate(120%);-webkit-backdrop-filter:blur(8px);}
body.light .sidebar{background:linear-gradient(180deg,rgba(255,255,255,0.70),rgba(255,255,255,0.65));border:1px solid rgba(13,18,25,0.05);}
body.dark .sidebar{background:linear-gradient(180deg,rgba(255,255,255,0.02),rgba(255,255,255,0.01));border:1px solid rgba(255,255,255,0.03);}

        .main{flex:1;display:flex;flex-direction:column;gap:18px;}
          .sidebar{display:none;}
        @media(max-width:920px){
            .sidebar{display:none;}
            .app{padding:14px;}
            .bottom-tab{display:flex;}
        }
    </style>
    </head>

<body class="dark">

<div class="app">

    
    <aside class="sidebar">
        <h2 style="font-weight:800;letter-spacing:1px;">EduSpark</h2>
        <nav style="width:100%;display:flex;flex-direction:column;gap:10px;text-align:center;">
            <a href="/" style="text-decoration:none;color:var(--muted);font-weight:600;">Home</a>
            <a href="/performance" style="text-decoration:none;color:var(--accent);font-weight:700;">Performance</a>
            <a href="#" style="text-decoration:none;color:var(--muted);font-weight:600;">Materials</a>
            <a href="#" style="text-decoration:none;color:var(--muted);font-weight:600;">Assessments</a>
            <a href="#" style="text-decoration:none;color:var(--muted);font-weight:600;">Forum</a>
        </nav>
    </aside>

    
    <main class="main">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

</div>

</body>
</html>
<?php /**PATH /Users/nguyuling/eduspark/resources/views/layouts/app.blade.php ENDPATH**/ ?>