<?php // views/salones/index.php ?>

<div class="page-header">
  <div>
    <h1><i class="ti ti-door" style="color:#534AB7;vertical-align:-3px;margin-right:8px"></i>Salones</h1>
    <p>Catálogo de aulas y espacios disponibles</p>
  </div>
  <a href="index.php?page=salones&action=create" class="btn btn-primary">
    <i class="ti ti-plus"></i> Nuevo salón
  </a>
</div>

<?php if (empty($salones)): ?>
  <div class="card">
    <div class="empty-state">
      <i class="ti ti-door" style="font-size:40px"></i>
      <p>No hay salones registrados aún.</p>
      <a href="index.php?page=salones&action=create" class="btn btn-primary" style="margin-top:14px">
        <i class="ti ti-plus"></i> Registrar primer salón
      </a>
    </div>
  </div>
<?php else: ?>

  <div class="table-wrap">
    <table class="sys-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Salón</th>
          <th>Ubicación</th>
          <th>Capacidad</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($salones as $i => $s): ?>
          <tr>
            <td style="color:#ADB5BD;font-size:12px"><?= $i + 1 ?></td>
            <td>
              <div style="display:flex;align-items:center;gap:10px">
                <div style="width:32px;height:32px;border-radius:8px;background:#E6F1FB;
                            color:#0C447C;display:grid;place-items:center;font-size:15px;flex-shrink:0">
                  <i class="ti ti-door"></i>
                </div>
                <span style="font-weight:500"><?= htmlspecialchars($s['nombre']) ?></span>
              </div>
            </td>
            <td style="color:#495057;font-size:13px">
              <?= $s['ubicacion'] ? htmlspecialchars($s['ubicacion']) : '<span style="color:#ADB5BD">—</span>' ?>
            </td>
            <td>
              <?php if ($s['capacidad']): ?>
                <span style="display:inline-flex;align-items:center;gap:5px;font-size:13px">
                  <i class="ti ti-users" style="color:#ADB5BD;font-size:13px"></i>
                  <?= $s['capacidad'] ?> personas
                </span>
              <?php else: ?>
                <span style="color:#ADB5BD">—</span>
              <?php endif; ?>
            </td>
            <td>
              <span class="badge-status <?= $s['estado']==1 ? 'active' : 'inactive' ?>">
                <?= $s['estado']==1 ? 'Disponible' : 'Inactivo' ?>
              </span>
            </td>
            <td>
              <div style="display:flex;gap:6px">
                <a href="index.php?page=salones&action=edit&id=<?= $s['id_salon'] ?>"
                   class="btn btn-sm" title="Editar">
                  <i class="ti ti-edit"></i>
                </a>
                <a href="index.php?page=salones&action=toggle&id=<?= $s['id_salon'] ?>"
                   class="btn btn-sm" title="<?= $s['estado']==1 ? 'Desactivar' : 'Activar' ?>">
                  <i class="ti ti-power" style="color:<?= $s['estado']==1 ? '#C0392B' : '#1D9E75' ?>"></i>
                </a>
                <button class="btn btn-sm btn-danger" title="Eliminar"
                        onclick="confirmDelete(
                          'index.php?page=salones&action=delete&id=<?= $s['id_salon'] ?>',
                          '<?= addslashes($s['nombre']) ?>')">
                  <i class="ti ti-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <p style="font-size:12.5px;color:#ADB5BD;margin-top:12px">
    <i class="ti ti-info-circle" style="vertical-align:-2px"></i>
    Solo los salones <strong>Disponibles</strong> aparecen al registrar horarios.
  </p>

<?php endif; ?>