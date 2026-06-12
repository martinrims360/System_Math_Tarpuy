<?php // views/areas/index.php ?>

<div class="page-header">
  <div>
    <h1><i class="ti ti-tag" style="color:#534AB7;vertical-align:-3px;margin-right:8px"></i>Áreas Temáticas</h1>
    <p>Categorías de contenido matemático</p>
  </div>
  <a href="index.php?page=areas&action=create" class="btn btn-primary">
    <i class="ti ti-plus"></i> Nueva área
  </a>
</div>

<?php
// Colores rotativos para los badges de área
$colores = [
  ['bg'=>'#EEEDFE','color'=>'#3C3489'],
  ['bg'=>'#E1F5EE','color'=>'#085041'],
  ['bg'=>'#FAEEDA','color'=>'#633806'],
  ['bg'=>'#FAECE7','color'=>'#712B13'],
  ['bg'=>'#E6F1FB','color'=>'#0C447C'],
];
?>

<?php if (empty($areas)): ?>
  <div class="card">
    <div class="empty-state">
      <i class="ti ti-tag" style="font-size:40px"></i>
      <p>No hay áreas registradas aún.</p>
      <a href="/index.php?page=areas&action=create" class="btn btn-primary" style="margin-top:14px">
        <i class="ti ti-plus"></i> Crear primera área
      </a>
    </div>
  </div>
<?php else: ?>

  <!-- Cards de áreas -->
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px;margin-bottom:20px">
    <?php foreach ($areas as $i => $a):
      $c = $colores[$i % count($colores)];
    ?>
      <div style="background:#fff;border:1px solid #E9ECEF;border-radius:12px;
                  padding:16px 18px;display:flex;align-items:center;gap:12px">
        <div style="width:38px;height:38px;border-radius:10px;background:<?= $c['bg'] ?>;
                    color:<?= $c['color'] ?>;display:grid;place-items:center;font-size:18px;flex-shrink:0">
          <i class="ti ti-math"></i>
        </div>
        <div style="flex:1;min-width:0">
          <div style="font-size:13.5px;font-weight:500;white-space:nowrap;
                      overflow:hidden;text-overflow:ellipsis">
            <?= htmlspecialchars($a['nombre']) ?>
          </div>
        </div>
        <div style="display:flex;gap:4px;flex-shrink:0">
          <a href="index.php?page=areas&action=edit&id=<?= $a['id_area'] ?>"
             class="btn btn-sm btn-icon" title="Editar">
            <i class="ti ti-edit" style="font-size:14px"></i>
          </a>
          <button class="btn btn-sm btn-icon btn-danger" title="Eliminar"
                  onclick="confirmDelete(
                    'index.php?page=areas&action=delete&id=<?= $a['id_area'] ?>',
                    '<?= addslashes($a['nombre']) ?>')">
            <i class="ti ti-trash" style="font-size:14px"></i>
          </button>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <p style="font-size:12.5px;color:#ADB5BD">
    <i class="ti ti-info-circle" style="vertical-align:-2px"></i>
    <?= count($areas) ?> área<?= count($areas) != 1 ? 's' : '' ?> registrada<?= count($areas) != 1 ? 's' : '' ?>.
    Las áreas se usan al registrar temas desarrollados.
  </p>

<?php endif; ?>
