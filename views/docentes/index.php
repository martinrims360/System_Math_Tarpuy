<?php // views/docentes/index.php ?>

<div class="page-header">
  <div>
    <h1><i class="ti ti-users" style="color:#534AB7;vertical-align:-3px;margin-right:8px"></i>Docentes</h1>
    <p>Gestión de usuarios del sistema</p>
  </div>
  <a href="index.php?page=docentes&action=create" class="btn btn-primary">
    <i class="ti ti-user-plus"></i> Nuevo docente
  </a>
</div>

<?php if (empty($docentes)): ?>
  <div class="card">
    <div class="empty-state">
      <i class="ti ti-users-off"></i>
      <p>No hay docentes registrados aún.</p>
      <a href="index.php?page=docentes&action=create" class="btn btn-primary" style="margin-top:14px">
        <i class="ti ti-user-plus"></i> Registrar primer docente
      </a>
    </div>
  </div>

<?php else: ?>
  <!-- Resumen rápido -->
  <div style="display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap">
    <span style="font-size:13px;color:#495057">
      <strong><?= count($docentes) ?></strong> docentes en total ·
      <strong><?= count(array_filter($docentes, fn($d) => $d['estado']==1)) ?></strong> activos
    </span>
  </div>

  <div class="table-wrap">
    <table class="sys-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Nombre</th>
          <th>Correo</th>
          <th>Rol</th>
          <th>Estado</th>
          <th>Registrado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($docentes as $i => $d): ?>
          <tr>
            <td style="color:#ADB5BD;font-size:12px"><?= $i + 1 ?></td>
            <td>
              <div style="display:flex;align-items:center;gap:10px">
                <div style="width:32px;height:32px;border-radius:50%;background:#EEEDFE;
                            color:#534AB7;display:grid;place-items:center;
                            font-size:12px;font-weight:600;flex-shrink:0">
                  <?= strtoupper(substr($d['nombre'],0,2)) ?>
                </div>
                <span style="font-weight:500"><?= htmlspecialchars($d['nombre']) ?></span>
              </div>
            </td>
            <td style="color:#495057"><?= htmlspecialchars($d['correo']) ?></td>
            <td>
              <?php if ($d['rol'] === 'coordinador'): ?>
                <span class="badge-status" style="background:#EEEDFE;color:#3C3489">Coordinador</span>
              <?php else: ?>
                <span class="badge-status" style="background:#E6F1FB;color:#0C447C">Docente</span>
              <?php endif; ?>
            </td>
            <td>
              <span class="badge-status <?= $d['estado']==1 ? 'active' : 'inactive' ?>">
                <?= $d['estado']==1 ? 'Activo' : 'Inactivo' ?>
              </span>
            </td>
            <td style="color:#ADB5BD;font-size:12.5px">
              <?= date('d/m/Y', strtotime($d['created_at'])) ?>
            </td>
            <td>
              <div style="display:flex;gap:6px">
                <a href="index.php?page=docentes&action=edit&id=<?= $d['id_docente'] ?>"
                   class="btn btn-sm" title="Editar">
                  <i class="ti ti-edit"></i>
                </a>
                <a href="index.php?page=docentes&action=toggle&id=<?= $d['id_docente'] ?>"
                   class="btn btn-sm" title="<?= $d['estado']==1 ? 'Desactivar' : 'Activar' ?>">
                  <i class="ti ti-power <?= $d['estado']==1 ? '' : 'ti-rotate-90' ?>"
                     style="color:<?= $d['estado']==1 ? '#C0392B' : '#1D9E75' ?>"></i>
                </a>
                <?php if ($d['id_docente'] !== Auth::user()['id']): ?>
                  <button class="btn btn-sm btn-danger" title="Eliminar"
                          onclick="confirmDelete(
                            'index.php?page=docentes&action=delete&id=<?= $d['id_docente'] ?>',
                            '<?= addslashes($d['nombre']) ?>')">
                    <i class="ti ti-trash"></i>
                  </button>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>