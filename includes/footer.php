<footer class="site-footer mt-5">
    <div class="container">
        <div class="row g-4 align-items-start">
            <div class="col-lg-5">
                <div class="d-flex align-items-center gap-2 mb-2"><span class="brand-mark">A</span><strong>AFone Store</strong></div>
                <p class="text-soft mb-0">Website top up game dengan menu game populer,Harga Terbaik</p>
            </div>
            <div class="col-lg-3 col-6">
                <h6>Menu</h6>
                <a href="TopUp.php">Top Up Game</a>
                <a href="Jokigame.php">Joki Rank</a>
                <a href="kontak.php">Kontak</a>
                </li>
                <?php if (is_logged_in()): ?>
                    <li class="nav-item"><a class="btn btn-warning btn-sm fw-bold ms-lg-2" href="admin/index.php">Dashboard</a></li>
                    <li class="nav-item"><a class="btn btn-outline-light btn-sm ms-lg-1" href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="btn btn-warning btn-sm fw-bold ms-lg-2" href="login.php">Login Admin</a></li>
                <?php endif; ?>
            </ul>
            </div>
            <div class="col-lg-3 col-6">
                <h6>Kontak</h6>
                <a href="https://wa.me/6281949351883" target="_blank">WhatsApp</a>
                <a href="mailto:info@afonestore.com">Email</a>
            </div>
        </div>
        <div class="footer-bottom">© <?= date('Y') ?> AFone Store 2026</div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="assets/js/main.js"></script>

</body>
</html>
</body>
</html>
