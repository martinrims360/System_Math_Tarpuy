<?php // views/grupos/index.php ?>

<div class="page-header">
  <div>
    <h1><i class="ti ti-users-group" style="color:#534AB7;vertical-align:-3px;margin-right:8px"></i>Grupos de Preparación</h1>
    <p>Niveles y equipos del programa de concursos</p>
  </div>
  <a href="index.php?page=grupos&action=create" class="btn btn-primary">
    <i class="ti ti-plus"></i> Nuevo grupo
  </a>
</div>

<?php if (empty($grupos)): ?>
  <div class="card">
    <div class="empty-state">
      <i class="ti ti-users-group" style="font-size:40px"></i>
      <p>No hay grupos registrados aún.</p>
      <a href="index.php?page=grupos&action=create" class="btn btn-primary" style="margin-top:14px">
        <i class="ti ti-plus"></i> Crear primer grupo
      </a>
    </div>
  </div>
<?php else: ?>

  <div class="table-wrap">
    <table class="sys-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Nombre</th>
          <th>Descripción</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($grupos as $i => $g): ?>
          <tr>
            <td style="color:#ADB5BD;font-size:12px"><?= $i + 1 ?></td>
            <td>
              <div style="display:flex;align-items:center;gap:10px">
                <div style="width:32px;height:32px;border-radius:8px;background:#FAEEDA;
                            color:#633806;display:grid;place-items:center;font-size:14px;flex-shrink:0">
                  <i class="ti ti-users-group"></i>
                </div>
                <span style="font-weight:500"><?= htmlspecialchars($g['nombre']) ?></span>
              </div>
            </td>
            <td style="color:#495057;font-size:13px">
              <?= $g['descripcion'] ? htmlspecialchars($g['descripcion']) : '<span style="color:#ADB5BD">—</span>' ?>
            </td>
            <td>
              <span class="badge-status <?= $g['estado']==1 ? 'active' : 'inactive' ?>">
                <?= $g['estado']==1 ? 'Activo' : 'Inactivo' ?>
              </span>
            </td>
            <td>
              <div style="display:flex;gap:6px">
                <a href="index.php?page=grupos&action=edit&id=<?= $g['id_grupo'] ?>"
                   class="btn btn-sm" title="Editar">
                  <i class="ti ti-edit"></i>
                </a>
                <a href="index.php?page=grupos&action=toggle&id=<?= $g['id_grupo'] ?>"
                   class="btn btn-sm" title="<?= $g['estado']==1 ? 'Desactivar' : 'Activar' ?>">
                  <i class="ti ti-power" style="color:<?= $g['estado']==1 ? '#C0392B' : '#1D9E75' ?>"></i>
                </a>
                <button class="btn btn-sm btn-danger" title="Eliminar"
                        onclick="confirmDelete(
                          'index.php?page=grupos&action=delete&id=<?= $g['id_grupo'] ?>',
                          '<?= addslashes($g['nombre']) ?>')">
                  <i class="ti ti-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>