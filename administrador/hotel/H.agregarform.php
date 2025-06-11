<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="form-container">
            <form action="agregarmetodcod.php" method="post">
                <table class="form-table">
                    <tr>
                        <th>Método</th>
                        <td>
                            <input type="text" name="nombre" class="form-input" 
                                   placeholder="Nombre del método" required>
                        </td>
                    </tr>
                    <tr>
                        <th>Porcentaje</th>
                        <td>
                            <input type="number" step="0.01" name="porcentaje" class="form-input" 
                                   placeholder="0.00" required>
                        </td>
                    </tr>
                </table>
                
                <button type="submit" class="form-submit">
                    <i class="fas fa-plus-circle"></i> AGREGAR MÉTODO
                </button>
            </form>
        </div>
</body>
</html>