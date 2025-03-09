
<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
            <a class="navbar-brand" href="/dashboard">Sabar Sakdremo</a>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="/dashboard">Home</a>
                </li>

                <!--Keuangan-->
                <li class="nav-item dropdown">
                    <button class="nav-link dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown" aria-expanded="false" onkeyup="#">
                      Keuangan
                    </button>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="/dashboard/keuangan/kas">Kas</a></li>
                      <li><a class="dropdown-item" href="/dashboard/keuangan/utang">UtangPiutang</a></li>
                    </ul>
                  </li>

                <!--Penggiligan-->
                <li class="nav-item dropdown">
                    <button class="nav-link dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                      Penggilingan
                    </button>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="/dashboard/penggilingan/tenaga_kerja">Tenaga Kerja</a></li>
                      <li><a class="dropdown-item" href="/dashboard/penggilingan/aset">Produk</a></li>
                      <li><a class="dropdown-item" href="/dashboard/penggilingan/penitipan">Stock</a></li>
                      <li><a class="dropdown-item" href="/dashboard/penggilingan/mesin">Mesin</a></li>
                      <li><a class="dropdown-item" href="/dashboard/penggilingan/perbaikan">Perbaikan Mesin</a></li>
                    </ul>
                  </li>

                  <!--pelanggan -->
                  <li class="nav-item dropdown">
                    <button class="nav-link dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                      Pelanggan
                    </button>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="/dashboard/penggilingan/pelanggan">Pelanggan</a></li>
                      <li><a class="dropdown-item" href="/dashboard/penggilingan/penitipan">Penitipan</a></li>
                    </ul>
                  </li>

                <!--Laporan-->
                <li class="nav-item dropdown">
                    <button class="nav-link dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                      Laporan
                    </button>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="/dashboard/laporan/arus_kas">Arus Kas</a></li>
                      <li><a class="dropdown-item" href="/dashboard/laporan/utang_piutang">Utang Piutang</a></li>
                      <li><a class="dropdown-item" href="/dashboard/laporan/laba_rugi">Laba Rugi</a></li>
                      <li><a class="dropdown-item" href="/dashboard/laporan/stock">Stock</a></li>
                    </ul>
                  </li>
            </ul>
            <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
            <!-- Dropdown -->
            <button class="nav-link dropdown-toggle d-flex" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="{{ asset('images/profil/'.Auth::user()->foto_user)}}" width="40px" height="40px" alt="profil"><br>
                <p>{{ Auth::user()->username}}</p>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="/dashboard/profil">profil</a></li>
                <li><a class="dropdown-item" href="/dashboard/profil/pengaturan">pengaturan</a></li>
                <li><a class="dropdown-item" href="/logout">Log Out</a></li>
            </ul>
        </div>
    </div>
</nav>
