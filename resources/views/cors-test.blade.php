<!doctype html><meta charset="utf-8">
<h1>Test CORS</h1>
<pre id="out">En cours…</pre>
<script>
(async () => {
  try {
    const r = await fetch('http://abdoupharma.test/api/v1/products', {
      headers: { 'Accept': 'application/json' }
    });
    const j = await r.json();
    out.textContent = 'OK CORS:\n' + JSON.stringify(j, null, 2);
  } catch (e) {
    out.textContent = 'Erreur CORS:\n' + e;
  }
})();
</script>
