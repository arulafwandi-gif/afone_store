<?php
require_once __DIR__ . '/includes/helpers.php';
$slug = trim($_GET['slug'] ?? '');
$game = $slug ? get_game_by_slug($slug) : null;
if (!$game || (int)$game['is_active'] !== 1) {
    flash('Game tidak ditemukan atau sedang nonaktif.', 'danger');
    redirect('TopUp.php');
}
$packages = get_packages_by_game((int)$game['id']);
$firstPackage = $packages[0] ?? null;
$pageTitle = 'Top Up ' . $game['name'] . ' - AFone Store';
$activePage = 'topup';
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <div class="page-hero-box">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="game-logo">
                    <?php if (!empty($game['image_url'])): ?><img src="<?= e(image_src($game['image_url'])) ?>" alt="<?= e($game['name']) ?>"><?php else: ?><?= e($game['icon_emoji'] ?: '🎮') ?><?php endif; ?>
                </div>
                <div>
                    <div class="section-kicker mb-2">Top Up <?= e(category_label($game['category'])) ?></div>
                    <h1 class="section-title mb-1">Top Up <?= e($game['name']) ?></h1>
                    <p class="text-soft mb-0"><?= e($game['description']) ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container my-4">
    <div class="content-card mb-3">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="section-kicker mb-2">Cara Top Up</div>
                <h3 class="fw-bold mb-2">Ikuti alur order seperti website top up game pada umumnya.</h3>
                <p class="text-soft mb-0">Masukkan ID akun, pilih nominal, masukkan kode promo bila ada, pilih pembayaran, lalu isi kontak aktif.</p>
            </div>
            <a href="TopUp.php" class="btn btn-outline-warning">Pilih Game Lain</a>
        </div>
        <div class="topup-guide-grid mt-4">
            <div class="guide-item"><span>1</span><strong>Masukkan ID Game</strong><small>User ID, Zone ID, server, atau tagline.</small></div>
            <div class="guide-item"><span>2</span><strong>Pilih Nominal</strong><small>Diamond, UC, VP, Robux, CP, coin, atau pass.</small></div>
            <div class="guide-item"><span>3</span><strong>Pilih Pembayaran</strong><small>QRIS, e-wallet, atau transfer bank.</small></div>
            <div class="guide-item"><span>4</span><strong>Kirim Order</strong><small>Admin memproses pesanan dari dashboard.</small></div>
        </div>
    </div>

    <form method="post" action="order.php" class="topup-layout">
        <input type="hidden" name="game_id" value="<?= (int)$game['id'] ?>">
        <div>
            <div class="step-card">
                <h4 class="fw-bold mb-3"><span class="step-number">1</span>Masukkan ID pengguna</h4>
                <div class="alert alert-dark border-warning text-soft"><?= e($game['instruction']) ?></div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">ID Pengguna / Player ID</label>
                        <input type="text" name="user_id" class="form-control" placeholder="Contoh: 12345678" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">ID Zona / Server / Tagline</label>
                        <input type="text" name="zone_id" class="form-control" placeholder="Contoh: 1234 / Asia / #1234">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nickname</label>
                        <input type="text" name="nickname" class="form-control" placeholder="Opsional, untuk validasi manual">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Catatan Akun</label>
                        <input type="text" name="account_note" class="form-control" placeholder="Contoh: server Indonesia / login via Moonton">
                    </div>
                </div>
                <p class="text-soft small mb-0 mt-3">Petunjuk: untuk Mobile Legends biasanya ID ditulis seperti <strong>12345678 (1234)</strong>. Pastikan data benar karena kesalahan ID menjadi tanggung jawab pembeli.</p>
            </div>

            <div class="step-card">
                <h4 class="fw-bold mb-3"><span class="step-number">2</span>Pilih nominal top up</h4>
                <?php if (!$packages): ?>
                    <div class="empty-state">Belum ada nominal untuk game ini.</div>
                <?php else: ?>
                    <div class="package-grid">
                        <?php foreach ($packages as $index => $package): ?>
                            <label class="package-option">
                                <input type="radio" name="package_id" value="<?= (int)$package['id'] ?>" data-name="<?= e($package['name']) ?>" data-price="<?= (float)$package['price'] ?>" <?= $index === 0 ? 'checked' : '' ?> required>
                                <div class="package-box">
                                    <?php if (!empty($package['badge'])): ?><span class="package-badge"><?= e($package['badge']) ?></span><?php endif; ?>
                                    <div class="package-name"><?= e($package['name']) ?></div>
                                    <div class="text-soft small"><?= e($package['description']) ?></div>
                                    <div class="package-price mt-2"><?= rupiah($package['price']) ?></div>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="step-card">
                <h4 class="fw-bold mb-3"><span class="step-number">3</span>Punya kode promo?</h4>
                <div class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label">Kode Promo <span class="text-soft fw-normal">(opsional)</span></label>
                        <input type="text" name="promo_code" id="promoCode" class="form-control" placeholder="Contoh: HEMAT5 / NEWUSER / AFONE10">
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-outline-warning w-100" id="applyPromo">Gunakan</button>
                    </div>
                </div>
                <p class="text-soft small mb-0 mt-3">Kode contoh yang aktif: <strong>HEMAT5</strong>, <strong>NEWUSER</strong>, <strong>AFONE10</strong>. Kamu bisa ubah logikanya di <code>includes/helpers.php</code>.</p>
            </div>

            <div class="step-card">
                <h4 class="fw-bold mb-3"><span class="step-number">4</span>Pilih saluran pembayaran</h4>
                <?php $paymentIndex = 0; foreach (grouped_payment_channels() as $groupName => $channels): ?>
                    <div class="payment-group-title"><?= e($groupName) ?></div>
                    <div class="payment-grid mb-3">
                        <?php foreach ($channels as $channel): ?>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="<?= e($channel['name']) ?>" data-label="<?= e($channel['label']) ?>" <?= $paymentIndex === 0 ? 'checked' : '' ?> required>
                                <span class="payment-box"><strong><?= e($channel['label']) ?></strong><small><?= e($channel['note']) ?></small></span>
                            </label>
                            <?php $paymentIndex++; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="step-card">
                <h4 class="fw-bold mb-3"><span class="step-number">5</span>Masukkan kontak dan data refund</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nomor WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control" placeholder="08xxxxxxxxxx" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="email@contoh.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jenis Akun Refund</label>
                        <select name="refund_account_type" class="form-select">
                            <option value="">Pilih jika ingin diisi</option>
                            <option value="BCA">BCA</option>
                            <option value="BRI">BRI</option>
                            <option value="BNI">BNI</option>
                            <option value="Mandiri">Mandiri</option>
                            <option value="DANA">DANA</option>
                            <option value="OVO">OVO</option>
                            <option value="GoPay">GoPay</option>
                            <option value="ShopeePay">ShopeePay</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nomor Akun Refund</label>
                        <input type="text" name="refund_account_number" class="form-control" placeholder="Nomor rekening / nomor e-wallet">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Catatan tambahan</label>
                        <textarea name="notes" rows="3" class="form-control" placeholder="Contoh: proses setelah jam 19.00"></textarea>
                    </div>
                </div>
                <p class="text-soft small mb-0 mt-3">Data refund berguna jika transaksi penuh, stok nominal kosong, atau pembayaran harus dikembalikan.</p>
            </div>
        </div>
        <aside class="order-sticky">
            <div class="content-card">
                <h4 class="fw-bold mb-3">Ringkasan Order</h4>
                <div class="summary-line"><span class="text-soft">Game</span><strong><?= e($game['name']) ?></strong></div>
                <div class="summary-line"><span class="text-soft">Nominal</span><strong id="selectedPackage">-</strong></div>
                <div class="summary-line"><span class="text-soft">Harga</span><strong id="selectedSubtotal">Rp 0</strong></div>
                <div class="summary-line"><span class="text-soft">Promo</span><strong class="text-success" id="selectedDiscount">- Rp 0</strong></div>
                <div class="summary-line"><span class="text-soft">Pembayaran</span><strong id="selectedPayment">QRIS</strong></div>
                <div class="summary-line"><span class="text-soft">Total</span><strong class="summary-price" id="selectedPrice">Rp 0</strong></div>
                <button class="btn btn-warning w-100 fw-bold mt-3" type="submit" <?= !$packages ? 'disabled' : '' ?>>Pesan Sekarang</button>
                <a class="btn btn-outline-light w-100 mt-2" href="TopUp.php">Pilih Game Lain</a>
                <p class="text-soft small mb-0 mt-3">Total akan dihitung ulang oleh server saat order dikirim.</p>
            </div>
        </aside>
    </form>
</section>
<script>
const promos = {HEMAT5:{type:'percent',value:5}, NEWUSER:{type:'flat',value:2000}, AFONE10:{type:'percent',value:10}};
function formatRupiah(value){
    return new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',maximumFractionDigits:0}).format(value).replace('IDR','Rp');
}
function promoDiscount(code, subtotal){
    code = (code || '').trim().toUpperCase();
    if(!promos[code]) return 0;
    const p = promos[code];
    const discount = p.type === 'percent' ? Math.round(subtotal * (p.value / 100)) : p.value;
    return Math.max(0, Math.min(subtotal, discount));
}
function updateSummary(){
    const selected = document.querySelector('input[name="package_id"]:checked');
    if(!selected) return;
    const subtotal = Number(selected.dataset.price || 0);
    const code = document.getElementById('promoCode')?.value || '';
    const discount = promoDiscount(code, subtotal);
    const payment = document.querySelector('input[name="payment_method"]:checked');
    const total = Math.max(0, subtotal - discount);
    document.getElementById('selectedPackage').textContent = selected.dataset.name || '-';
    document.getElementById('selectedSubtotal').textContent = formatRupiah(subtotal);
    document.getElementById('selectedDiscount').textContent = '- ' + formatRupiah(discount);
    document.getElementById('selectedPayment').textContent = payment ? (payment.dataset.label || payment.value) : '-';
    document.getElementById('selectedPrice').textContent = formatRupiah(total);
}
document.querySelectorAll('input[name="package_id"], input[name="payment_method"]').forEach(el => el.addEventListener('change', updateSummary));
document.getElementById('applyPromo')?.addEventListener('click', updateSummary);
document.getElementById('promoCode')?.addEventListener('input', updateSummary);
updateSummary();
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
