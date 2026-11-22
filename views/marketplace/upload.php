<div style="padding-top: 9rem;">
    <div style="max-width: 80rem; margin: 0 auto; padding: 0 2rem;">
        <h1 style="font-size: 2.8rem; font-weight: 800; margin-bottom: 0.5rem;">Publicar Recurso</h1>
        <p style="color: #94A3B8; margin-bottom: 3rem;">Comparte tu conocimiento con la comunidad UAEMex</p>

        <?php
            include_once __DIR__ . '/../templates/alertas.php';
        ?>

        <form method="POST" action="/marketplace/subir" class="formulario" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>

            <div class="formulario__campo">
                <label for="titulo" class="formulario__label">Título del Recurso</label>
                <input 
                    type="text" 
                    class="formulario__input" 
                    id="titulo" 
                    name="titulo"
                    placeholder="Ej: Apuntes de Cálculo Diferencial — Unidad 3"
                    value="<?php echo s($asset->titulo ?? ''); ?>"
                >
            </div>

            <div class="formulario__campo">
                <label for="descripcion" class="formulario__label">Descripción</label>
                <textarea 
                    class="formulario__textarea" 
                    id="descripcion" 
                    name="descripcion"
                    placeholder="Describe el contenido, para quién es útil, qué temas cubre..."
                ><?php echo s($asset->descripcion ?? ''); ?></textarea>
            </div>

            <div class="formulario__row">
                <div class="formulario__campo">
                    <label for="category_id" class="formulario__label">Categoría</label>
                    <select class="formulario__select" id="category_id" name="category_id">
                        <option value="">— Seleccionar —</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?php echo $cat->id; ?>" <?php echo ($asset->category_id ?? 0) === $cat->id ? 'selected' : ''; ?>>
                                <?php echo s($cat->nombre); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="formulario__campo">
                    <label for="precio" class="formulario__label">Precio (MXN) — Usa 0 para gratis</label>
                    <input 
                        type="number" 
                        class="formulario__input" 
                        id="precio" 
                        name="precio"
                        min="0" 
                        step="0.01"
                        placeholder="0.00"
                        value="<?php echo $asset->precio ?? 0; ?>"
                    >
                </div>
            </div>

            <div class="formulario__row">
                <div class="formulario__campo">
                    <label for="faculty_id" class="formulario__label">Facultad</label>
                    <select class="formulario__select" id="faculty_id" name="faculty_id">
                        <option value="">— Seleccionar —</option>
                        <?php foreach($faculties as $f): ?>
                            <option value="<?php echo $f->id; ?>" <?php echo ($asset->faculty_id ?? 0) === $f->id ? 'selected' : ''; ?>>
                                <?php echo s($f->nombre); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="formulario__campo">
                    <label for="career_id" class="formulario__label">Carrera (opcional)</label>
                    <select class="formulario__select" id="career_id" name="career_id">
                        <option value="">— Todas las carreras —</option>
                    </select>
                </div>
            </div>

            <div class="formulario__campo">
                <label for="archivo" class="formulario__label">Archivo</label>
                <input 
                    type="file" 
                    class="formulario__file" 
                    id="archivo" 
                    name="archivo"
                    accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar,.txt,.md"
                >
                <p style="font-size: 1.2rem; color: #64748B; margin-top: 0.5rem;">
                    Formatos: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP, RAR, TXT, MD
                </p>
            </div>

            <input type="submit" class="formulario__submit" value="Publicar Recurso">
        </form>
    </div>
</div>

<script>
document.getElementById('faculty_id').addEventListener('change', async function() {
    const facultyId = this.value;
    const careerSelect = document.getElementById('career_id');
    careerSelect.innerHTML = '<option value="">— Todas las carreras —</option>';
    
    if (!facultyId) return;
    
    try {
        const response = await fetch('/api/careers?faculty_id=' + facultyId);
        const careers = await response.json();
        careers.forEach(career => {
            const option = document.createElement('option');
            option.value = career.id;
            option.textContent = career.nombre;
            careerSelect.appendChild(option);
        });
    } catch(e) { console.error(e); }
});
</script>
