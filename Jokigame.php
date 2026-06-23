<?php
require_once __DIR__ . '/includes/helpers.php';
$pageTitle = 'Joki Rank Mobile Legends - AFone Store';
$activePage = 'joki';
$regular = get_joki_services('reguler', true);
$express = get_joki_services('express', true);
$ranks = [];
foreach ($regular as $service) $ranks[$service['rank_name']] = (int)$service['rank_order'];
asort($ranks);
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <div class="page-hero-box">
            <div class="section-kicker mb-2">Joki Rank</div>
            <h1 class="section-title mb-2">Joki Ranked Mobile Legends</h1>
            <p class="text-soft mb-0">Ada daftar harga reguler/express dan kalkulator estimasi biaya. Desain tetap mengikuti tema orange-hitam AFone Store.</p>
        </div>
    </div>
</section>

<section class="container my-4">
    <div class="content-card mb-4">
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-3">
            <div>
                <div class="section-kicker mb-1">Daftar Harga</div>
                <h2 class="section-title mb-0">Pilih tipe layanan joki</h2>
            </div>
        </div>
        <ul class="nav nav-pills gap-2 mb-4" id="jokiTab" role="tablist">
            <li class="nav-item" role="presentation"><button class="btn btn-outline-warning active" data-bs-toggle="tab" data-bs-target="#regularTab" type="button">Reguler</button></li>
            <li class="nav-item" role="presentation"><button class="btn btn-outline-warning" data-bs-toggle="tab" data-bs-target="#expressTab" type="button">Express</button></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="regularTab">
                <div class="joki-price-grid">
                    <?php foreach ($regular as $item): ?>
                        <div class="joki-price-card"><span><?= e($item['icon'] ?: '🏆') ?></span><div><strong><?= e($item['rank_name']) ?></strong><small><?= rupiah($item['price']) ?></small></div></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="tab-pane fade" id="expressTab">
                <div class="joki-price-grid">
                    <?php foreach ($express as $item): ?>
                        <div class="joki-price-card"><span><?= e($item['icon'] ?: '⚡') ?></span><div><strong><?= e($item['rank_name']) ?></strong><small><?= rupiah($item['price']) ?></small></div></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <form action="joki-order.php" method="post" class="content-card mb-4" id="jokiCalculator">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="section-kicker mb-1">Kalkulator Joki</div>
                <h2 class="section-title mb-3">Hitung estimasi harga</h2>
                <h5 class="joki-subtitle">Rank Saat Ini</h5>
                <div class="row g-3 mb-3">
                    <div class="col-md-7"><label class="form-label">Rank</label><select name="current_rank" id="currentRank" class="form-select" required><option value="">Pilih Rank</option><?php foreach ($ranks as $rank => $order): ?><option value="<?= e($rank) ?>"><?= e($rank) ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-5"><label class="form-label">Bintang</label><input type="number" name="current_star" id="currentStar" class="form-control" value="0" min="0" max="100"></div>
                </div>
                <h5 class="joki-subtitle">Rank Tujuan</h5>
                <div class="row g-3 mb-3">
                    <div class="col-md-7"><label class="form-label">Rank</label><select name="target_rank" id="targetRank" class="form-select" required><option value="">Pilih Rank</option><?php foreach ($ranks as $rank => $order): ?><option value="<?= e($rank) ?>"><?= e($rank) ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-5"><label class="form-label">Bintang</label><input type="number" name="target_star" id="targetStar" class="form-control" value="0" min="0" max="100"></div>
                </div>
                <h5 class="joki-subtitle">Tipe Layanan</h5>
                <div class="joki-radio-grid mb-3">
                    <label><input type="radio" name="service_type" value="reguler" checked><span>Reguler</span></label>
                    <label><input type="radio" name="service_type" value="express"><span>Express</span></label>
                </div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">WhatsApp</label><input type="text" name="whatsapp" class="form-control" placeholder="08xxxxxxxxxx" required></div>
                    <div class="col-md-6"><label class="form-label">Nickname / ID Akun</label><input type="text" name="account_info" class="form-control" placeholder="Nickname / ID akun"></div>
                    <div class="col-12"><label class="form-label">Catatan</label><textarea name="notes" class="form-control" rows="3" placeholder="Contoh: push malam / hero request"></textarea></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="joki-summary-box">
                    <div class="section-kicker">Estimasi Harga</div>
                    <div class="summary-price my-2" id="jokiTotal">Rp 0</div>
                    <input type="hidden" name="estimated_price" id="estimatedPrice" value="0">
                    <p class="text-soft small">Harga dihitung dari rank yang dipilih. Admin tetap bisa konfirmasi ulang jika data akun berbeda.</p>
                    <button class="btn btn-warning w-100 fw-bold" type="submit">Buat Order Joki</button>
                </div>
            </div>
        </div>
    </form>
</section>
<script>
const rankOrder = <?= json_encode($ranks, JSON_UNESCAPED_UNICODE) ?>;
const priceMap = {
    reguler: <?= json_encode(joki_price_map('reguler'), JSON_UNESCAPED_UNICODE) ?>,
    express: <?= json_encode(joki_price_map('express'), JSON_UNESCAPED_UNICODE) ?>
};
function rupiahJoki(v){ return new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',maximumFractionDigits:0}).format(v).replace('IDR','Rp'); }
function updateJokiTotal(){
    const current = document.getElementById('currentRank').value;
    const target = document.getElementById('targetRank').value;
    const cStar = Number(document.getElementById('currentStar').value || 0);
    const tStar = Number(document.getElementById('targetStar').value || 0);
    const type = document.querySelector('input[name="service_type"]:checked')?.value || 'reguler';
    let total = 0;
    if(current && target && rankOrder[current] !== undefined && rankOrder[target] !== undefined){
        const start = Number(rankOrder[current]);
        const end = Number(rankOrder[target]);
        const names = Object.keys(rankOrder);
        names.forEach(name => {
            const order = Number(rankOrder[name]);
            if(order > start && order <= end) total += Number(priceMap[type][name] || 0);
        });
        if(end === start) total = Math.max(0, (tStar - cStar)) * Number(priceMap[type][target] || 0);
        if(total === 0 && end > start) total = Number(priceMap[type][target] || 0);
    }
    document.getElementById('jokiTotal').textContent = rupiahJoki(total);
    document.getElementById('estimatedPrice').value = total;
}
document.querySelectorAll('#jokiCalculator select,#jokiCalculator input').forEach(el => el.addEventListener('input', updateJokiTotal));
document.querySelectorAll('#jokiCalculator select,#jokiCalculator input').forEach(el => el.addEventListener('change', updateJokiTotal));
updateJokiTotal();
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
