@props(['url'])

<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
            
            {{-- Usamos una TABLA para alinear icono y texto, es lo más seguro en emails --}}
            <table border="0" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                    {{-- 1. EL ICONO (Cuadro Naranja) --}}
                    <td style="background-color: #ea580c; padding: 10px; border-radius: 12px; vertical-align: middle;">
                        {{-- 
                           NOTA: FontAwesome NO funciona en correos. 
                           He puesto un emoji 🍴 que se ve similar.
                           Si tienes una imagen real del icono, usa la etiqueta <img> aquí.
                        --}}
                        <span style="font-size: 20px; line-height: 1;">🍴</span>
                    </td>

                    {{-- Espacio entre icono y texto --}}
                    <td width="12">&nbsp;</td>

                    {{-- 2. EL TEXTO --}}
                    <td style="vertical-align: middle; text-align: left;">
                        <span style="
                            display: block; 
                            font-family: Helvetica, Arial, sans-serif; 
                            font-size: 20px; 
                            font-weight: bold; 
                            color: #ffffff; /* Texto blanco (asegúrate que el fondo del header sea oscuro) */
                            line-height: 1;
                            letter-spacing: 0.5px;
                        ">
                            K-HAMBURGUESAS
                        </span>
                        
                        <span style="
                            display: block; 
                            font-family: 'Courier New', Courier, monospace; /* Simulando font-mono */
                            font-size: 10px; 
                            color: #f97316; /* Naranja 500 */
                            letter-spacing: 3px; /* tracking-[0.25em] */
                            font-weight: bold; 
                            text-transform: uppercase; 
                            margin-top: 4px;
                        ">
                            Restaurante
                        </span>
                    </td>
                </tr>
            </table>

        </a>
    </td>
</tr>