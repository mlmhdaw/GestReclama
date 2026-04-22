<aside class="sidebar">
  <nav class="sidebar__nav">

    <a href="/index.php"
       class="sidebar__item <?= $menuActivo === 'panel' ? 'sidebar__item--active' : '' ?>">
      Panel principal
    </a>
    
    <a href="/crear_reclamacion.php"
       class="sidebar__item <?= $menuActivo === 'crear' ? 'sidebar__item--active' : '' ?>">
      Registrar reclamación
    </a>

    <a href="/listar_reclamaciones.php"
       class="sidebar__item <?= $menuActivo === 'listar' ? 'sidebar__item--active' : '' ?>">
      Consultar reclamaciones
    </a>

    <a href="/detalle_reclamacion.php"
       class="sidebar__item <?= $menuActivo === 'detalle' ? 'sidebar__item--active' : '' ?>">
      Seguir reclamación
    </a>

    <a href="#"
       class="sidebar__item <?= $menuActivo === 'administrar' ? 'sidebar__item--active' : '' ?>">
      Gestión administrativa
    </a>

  </nav>

  <div class="sidebar__footer">
    <a href="/logout.php" class="sidebar__item logout">
      Cerrar sesión
    </a>
  </div>
</aside>