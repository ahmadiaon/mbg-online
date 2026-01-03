<div class="left-side-bar">
    <div class="brand-logo">
        <a href="/">
            <img src="/assets/vendors/images/mbg_logo.png" alt="" class="dark-logo" />
            <img src="/assets/vendors/images/mbg_logo.png" alt="" class="light-logo" />
        </a>
        <div class="close-sidebar" data-toggle="left-sidebar-close">
            <i class="ion-close-round"></i>
        </div>
    </div>
    <div class="menu-block customscroll">
        <div class="sidebar-menu">
            <ul id="accordion-menu">
                <li class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle">
                        <span class="micon bi bi-house"></span><span class="mtext">Home</span>
                    </a>
                    <ul class="submenu">
                        <li hidden><a href="/">Dashboard 1</a></li>
                        <li hidden><a href="struktur-organisasi">SO</a></li>
                        <li id="app"><a href="/app">MENU</a></li>
                    </ul>
                </li>
                @if (session('FILTER_APP')['USER']['role'] > 1)
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>

                    <li>
                        <div class="sidebar-small-cap">MANAGE</div>
                    </li>
                    <li>
                        <a href="javascript:;" class="dropdown-toggle">
                            <span class="micon bi-calendar-check-fill"></span><span class="mtext">ABSENSI</span>
                        </a>
                        <ul class="submenu">
                            <li><a href="/manage/absensi">Absensi</a></li>
                            <li><a href="/manage/izin">Izin</a></li>
                            <li><a href="/manage/cuti">Cuti</a></li>
                        </ul>
                    </li>
                @endif
                @if (in_array('PAYROLL', session('FILTER_APP.DEFAULT_FILTER.FEATURE', [])))
                    <li>

                        <a href="javascript:;" id="payroll" class="dropdown-toggle">
                            <span class="micon bi-journal-bookmark-fill"></span><span class="mtext">PAYROLL</span>
                        </a>
                        <ul class="submenu">
                            <li id="slip"><a href="/payroll/slip">SLIP</a></li>
                        </ul>
                    </li>
                @endif
                {{-- @dump(session('FILTER_APP.DEFAULT_FILTER.FEATURE')) --}}
                @if (in_array('RECRUITMENT', session('FILTER_APP.DEFAULT_FILTER.FEATURE', [])))
                    <li>
                        <a href="/manage/recruitment" class="dropdown-toggle no-arrow">
                            <span class="micon bi bi-box-seam"></span><span class="mtext">Recruitment</span>
                        </a>
                    </li>
                @endif
                @if (session('FILTER_APP')['USER']['role'] >= 9)
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>

                    <li>
                        <div class="sidebar-small-cap">HAULING</div>
                    </li>
                    <li>
                        <a href="javascript:;" class="dropdown-toggle">
                            <span class="micon bi-calendar-check-fill"></span><span class="mtext">ABSENSI</span>
                        </a>
                        <ul class="submenu">
                            <li><a href="/hauling/time-cek">Rute</a></li>
                            <li><a href="/manage/izin">Izin</a></li>
                            <li><a href="/manage/cuti">Cuti</a></li>
                        </ul>
                    </li>
                @endif
                @if (session('FILTER_APP')['USER']['role'] >= 9)
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>

                    <li>
                        <div class="sidebar-small-cap">SUPER USER</div>
                    </li>
                    <li>
                        <a href="javascript:;" id="database" class="dropdown-toggle">
                            <span class="micon bi bi-egg-fried"></span><span class="mtext">DATABASE</span>
                        </a>
                        <ul class="submenu">
                            <li><a id="user" href="/database/user">User</a></li>
                            <li><a id="menu" href="/database/menu">Menu</a></li>
                            <li><a id="form" href="/database/form">Form</a></li>
                            <li><a id="data" href="/database/data">Database</a></li>
                            <li><a id="form" href="/database/blank">BLANK</a></li>
                        </ul>
                    </li>
                @endif
                <li>
                    <div class="dropdown-divider"></div>
                </li>
                <li>
                    <div class="sidebar-small-cap">PROFIL SAYA</div>
                </li>
                <li>
                    <a href="/profile" id="profile" class="dropdown-toggle no-arrow">
                        <span class="micon bi bi-person-lines-fill"></span><span class="mtext">Profil</span>
                    </a>
                </li>
                <li hidden>
                    <a href="javascript:;" class="dropdown-toggle">
                        <span class="micon bi bi-calendar2-range"></span><span class="mtext">Kehadiran</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="#">Abseni</a></li>
                        <li><a href="#">Izin</a></li>
                        <li><a href="#">Cuti</a></li>
                    </ul>
                </li>
                <li hidden>
                    <a href="javascript:;" class="dropdown-toggle">
                        <span class="micon bi bi-graph-up-arrow"></span><span class="mtext">Personal</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="#">Hauling</a></li>
                        <li><a href="#">Overtime</a></li>
                        <li><a href="#">Payroll</a></li>
                    </ul>
                </li>
                <li>
                    <a href="/my-slip" id="my-slip" class="dropdown-toggle no-arrow">
                        <span class="micon bi bi-cash-stack"></span><span class="mtext">Slip</span>
                    </a>
                </li>

                <li>
                    <a href="/user" id="user" class="dropdown-toggle no-arrow">
                        <span class="micon bi bi-person-lines-fill"></span><span class="mtext">User</span>
                    </a>
                </li>
                <li>
                    <a href="sitemap.html" class="dropdown-toggle no-arrow">
                        <span class="micon bi bi-box-arrow-right"></span><span class="mtext">Keluar</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>
