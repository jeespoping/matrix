<script type="text/javascript">
    $(function(){
        $('.numerico').on({
            keypress: function(e) {
                var r = soloNumeros(e);
                if(r==true)
                {
                    var codeentr = (e.which) ? e.which : e.keyCode; /*if(codeentr == 13) { buscarDatosBasicos(); }*/
                    return true;
                }
                return false;
            }
        });
        simularPlaceHolder();

        $('#wconcepto_por_tiempo_<?=$identifica_concepto?>, #wcodigo_concepto_anest_<?=$identifica_concepto?>, #wcodigo_concepto_rango_por_rango_<?=$identifica_concepto?>').on({
            focusout: function(e) {
                if($(this).val().replace(/ /gi, "") == '')
                {
                    $(this).val("");
                    $(this).attr("codigo","");
                    $(this).attr("nombre","");
                }
                else
                {
                    $(this).val($(this).attr("nombre"));
                }
            }
        });

        $('#wprocedimiento_por_tiempo_<?=$identifica_concepto?>, #wprocedimiento_limit_<?=$identifica_concepto?>, #wcodigo_procedimiento_anest_<?=$identifica_concepto?>, #wcodigo_procedimiento_rango_por_rango_<?=$identifica_concepto?>').on({
            focusout: function(e) {
                if($(this).val().replace(/ /gi, "") == '')
                {
                    $(this).val("");
                    $(this).attr("codigo","");
                    $(this).attr("nombre","");
                }
                else
                {
                    $(this).val($(this).attr("nombre"));
                }
            }
        });

        $('.cobroHora_<?=$identifica_concepto?>').on({
                focusout: function(e) {
                    // alert('ok');
                    var identifica_concepto = $(this).attr('concepto');
                    var validar_config = true; // validar si hay info en otros tipos de cobro de este concepto
                    validar_config = validar_cambio_TipoCobro(identifica_concepto);

                    if(validar_config)
                    {
                        var wtiempo_minimo_hora = $("#wtiempo_minimo_hora_"+identifica_concepto).val();
                        var wcobro_por_hora     = ($("#wcobro_por_hora_"+identifica_concepto).is(":checked")) ? 'on': 'off';
                        var wconcepto_hora      = $("#wconcepto_por_tiempo_"+identifica_concepto).attr('codigo');
                        var wprocedimiento_hora = $("#wprocedimiento_por_tiempo_"+identifica_concepto).attr('codigo');

                        $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                            {
                                accion              : 'update',
                                form                : 'valores_cobro_hora',
                                consultaAjax        : '',
                                arr_politica        : $("#arr_politica").val(),
                                identifica_concepto : identifica_concepto,
                                wtiempo_minimo_hora : wtiempo_minimo_hora,
                                wcobro_por_hora     : wcobro_por_hora,
                                wconcepto_hora      : wconcepto_hora,
                                wprocedimiento_hora : wprocedimiento_hora
                            },
                            function(data){
                                if(data.error == 1)
                                {
                                    alert(data.mensaje);
                                }
                                else
                                {
                                }
                                return data;
                            },
                            "json"
                        ).done(function(data){
                                $("#arr_politica").val(data.arr_politica);
                        });
                    }
                    else
                    {
                        $("#wtiempo_minimo_hora_"+identifica_concepto).val("");
                        $("#wcobro_por_hora_"+identifica_concepto).removeAttr("checked");
                    }
                }
            });

        $('.requerido').on({
            focusout: function(e) {
                if($(this).is(":visible") && $(this).val().replace(/ /gi, "") == '')
                {
                    $(this).addClass("campoRequerido");
                }
                else
                {
                    $(this).removeClass("campoRequerido");
                }
            }
        });

        $('#wconcepto_cantidad_<?=$identifica_concepto?>').on({
            keyup: function(e) {
                    var cantidad = $(this).val();
                    var identifica_concepto = $(this).attr('concepto');
                    if(cantidad.replace(/ /gi, "") == '' || cantidad.replace(/0/gi, "") == '' || cantidad == '0')
                    {
                        cantidad = 1;
                    }
                    $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                        {
                            accion              : 'update',
                            form                : 'cantidad_concepto',
                            consultaAjax        : '',
                            arr_politica        : $("#arr_politica").val(),
                            identifica_concepto : identifica_concepto,
                            cantidad            : cantidad
                        },
                        function(data){
                            if(data.error == 1)
                            {
                                alert(data.mensaje);
                            }
                            else
                            {
                            }
                            return data;
                        },
                        "json"
                    ).done(function(data){
                            $("#arr_politica").val(data.arr_politica);
                    });
            }
        });
    });

    function autocompleteBuscadores(identifica_concepto)
    {
        var id_autocomp_grupos = $(":input[id^=wbuscador_materiales_"+identifica_concepto+"]").attr("id");
        arr_datos = new Array();
        //var datos = arr_wempresp;//eval( $("#arr_wempresp").val() );
        var datos = eval('(' + $("#arr_grupos_materiales").val() + ')');
        var index = -1;
        for (var CodVal in datos)
        {
            index++;
            arr_datos[index] = {};
            arr_datos[index].value  = CodVal+'-'+datos[CodVal];
            arr_datos[index].label  = CodVal+'-'+datos[CodVal];
            arr_datos[index].codigo = CodVal;
            arr_datos[index].nombre = CodVal+'-'+datos[CodVal];
        }
        // console.log(arr_datos);

        $("#"+id_autocomp_grupos).autocomplete({
                source: arr_datos, minLength : 0,
                select: function( event, ui ) {
                            // Lee el valor seleccionado en el autocompletar y lee solo el código y lo adiciona a otro campo de solo código.
                            var cod_sel = ui.item.codigo;
                            var nom_sel = ui.item.nombre;
                            $("#"+id_autocomp_grupos).attr("codigo",cod_sel);
                            $("#"+id_autocomp_grupos).attr("nombre",nom_sel);
                            // cargarConceptosPorProcedimientos(cod_sel);
                        }
        });
    }

    $(document).ready( function () {
        crearAutocomplete('arr_conceptos', 'wconcepto_por_tiempo_<?=$identifica_concepto?>', '<?=$wnuevo_concepto_gral_hora?>', '<?=$wnuevo_concepto_gral_hora?>-<?=$nombre_concepto_hora?>');
        crearAutocomplete('arr_procedimientos', 'wprocedimiento_por_tiempo_<?=$identifica_concepto?>', '<?=$wprocedimiento_hora?>', '<?=$wprocedimiento_hora?>-<?=$nombre_procedimiento_hora?>');

        crearAutocomplete('arr_conceptos', 'wcodigo_concepto_anest_<?=$identifica_concepto?>', '<?=$wnuevo_concepto_gral?>', '<?=$wnuevo_concepto_gral?>-<?=$nombre_concepto?>');
        crearAutocomplete('arr_procedimientos', 'wcodigo_procedimiento_anest_<?=$identifica_concepto?>', '<?=$wprocedimiento?>', '<?=$wprocedimiento?>-<?=$nombre_procedimiento?>');

        crearAutocomplete('arr_conceptos', 'wcodigo_concepto_rango_por_rango_<?=$identifica_concepto?>', '<?=$wnuevo_concepto_gral?>', '<?=$wnuevo_concepto_gral?>-<?=$nombre_concepto?>');
        crearAutocomplete('arr_procedimientos', 'wcodigo_procedimiento_rango_por_rango_<?=$identifica_concepto?>', '<?=$wprocedimiento?>', '<?=$wprocedimiento?>-<?=$nombre_procedimiento?>');

        //Para inicializar el procedimiento cuando se van a crear limites relacionados a UVR's
        crearAutocomplete('arr_procedimientos', 'wprocedimiento_limit_<?=$identifica_concepto?>', '<?=$wprocedimiento?>', '<?=$wprocedimiento?>-<?=$nombre_procedimiento?>');

        autocompleteBuscadores('<?=$identifica_concepto?>');

        var ConceptoInventario = '<?=$ConceptoInventario?>';
        if(ConceptoInventario == 'on')
        {
            llamar_insumos('contenedor_inventario_<?=$identifica_concepto?>','no','<?=$identifica_concepto?>');
        }
    });
</script>

<?php
include_once("conex.php");
$default_horas = '';
$default_es_cobro_horas = '';
if($tipo_cobro=='cobro_hora')
{
    $default_horas = $arr_temp_cb_x_horas['wtiempo_minimo_hora'];
    $default_es_cobro_horas = ($arr_temp_cb_x_horas['wcobro_por_hora'] == 'on') ? 'checked="checked"' : '';
}
?>

<!-- CONCEPTOS PLANTILLA -->
<input id="arr_politica_temp" name="arr_politica_temp" value="<?=base64_encode(serialize($arr_politica))?>" type="hidden">
<input id="arr_conceptos_pol_temp" name="arr_conceptos_pol_temp" value='<?=$arr_conceptos_pol_temp?>' type="hidden">
<input id="arr_conceptos_pol_b64_temp" name="arr_conceptos_pol_b64_temp" value='<?=$arr_conceptos_pol_b64?>' type="hidden">
<div id='div_concepto_agregado_<?=$identifica_concepto?>'>
    <h3>&nbsp;&nbsp;&nbsp;<?=$identifica_concepto.'-'.$nombre_concepto?></h3>
    <div id="contenedor_interior_concepto_<?=$identifica_concepto?>">
        <div style="display:none;" class=""><span class="encabezadoTabla">Cantidad concepto:</span> <span class="fila2"><input type="text" id="wconcepto_cantidad_<?=$identifica_concepto?>" concepto="<?=$identifica_concepto?>" value="<?=$valor_cantidad_concepto?>" placeholder="Cantidad" size="6" class="numerico requerido" <?=(($ConceptoInventario=='on')? 'disabled="disabled"':'')?>></span></div>
        <div align="right" style="font-size:8pt;">
            Para quitar concepto "<?=$nombre_concepto?>" de la plantilla:
            <span style="color:red;font-weigth:bold; cursor:pointer;" onclick="eliminarConceptoView('<?=$identifica_concepto?>'); eliminarConcepto('<?=$wnuevo_concepto_gral?>');">[Eliminar]</span>
        </div>
        <?php
        if($ConceptoInventario != 'on')
        {
        ?>
        <fieldset id="field_set_concepto_<?=$identifica_concepto?>">
            <legend>Tipos de cobro por:</legend>
            <!-- Tabs Left -->
            <ul class="pestania left" id="pestania">
                <li <?=(($tipo_cobro=='cobro_hora') ? 'class="current"': '')?> onclick="" >
                    <!-- <input <?=(($tipo_cobro=='cobro_hora') ? 'checked="checked"': '')?> type="radio" id="wtipocobro1_<?=$identifica_concepto?>" name="wtipocobro_<?=$identifica_concepto?>" value="1" checked="checked" onclick="$(this).next('a[tabLink<?=$identifica_concepto?>^=\'#\']').click();"> -->
                    <a href="javascript:" tabLink<?=$identifica_concepto?>="#tabr1_<?=$identifica_concepto?>">
                    Hora
                    </a>
                </li>
                <li <?=(($tipo_cobro=='cobro_anestesia') ? 'class="current"': '')?> onclick="" >
                    <!-- <input <?=(($tipo_cobro=='cobro_anestesia') ? 'checked="checked"': '')?> type="radio" id="wtipocobro2_<?=$identifica_concepto?>" name="wtipocobro_<?=$identifica_concepto?>" value="2"  onclick="$(this).next('a[tabLink<?=$identifica_concepto?>^=\'#\']').click();"> -->
                    <a href="javascript:" tabLink<?=$identifica_concepto?>="#tabr2_<?=$identifica_concepto?>">
                    Tipo anestesia
                    </a>
                </li>
                <li <?=(($tipo_cobro=='cobro_uso') ? 'class="current"': '')?> onclick="" >
                    <!-- <input <?=(($tipo_cobro=='cobro_uso') ? 'checked="checked"': '')?> type="radio" id="wtipocobro3_<?=$identifica_concepto?>" name="wtipocobro_<?=$identifica_concepto?>" value="3"  onclick="$(this).next('a[tabLink<?=$identifica_concepto?>^=\'#\']').click();"> -->
                    <a href="javascript:" tabLink<?=$identifica_concepto?>="#tabr3_<?=$identifica_concepto?>">
                    Tiempo de uso en rangos
                    </a>
                </li>
            </ul>
            <!-- Tabs Left -->

            <!-- CONTENIDO DE LAS PESTAÑAS -->
            <!-- CONTENIDO COBRO POR HORAS -->
            <div id="tabr1_<?=$identifica_concepto?>" class="tab-content">
                <table align="center">
                    <tr>
                        <td class="fila1">Tiempo m&iacute;nimo (Minutos)</td>
                        <td class="fila2" >
                            <input concepto="<?=$identifica_concepto?>" type="text" id="wtiempo_minimo_hora_<?=$identifica_concepto?>" name="wtiempo_minimo_hora_<?=$identifica_concepto?>" value="<?=$default_horas?>" placeholder="Minutos" maxlength="10" size="5" class="numerico cobroHora_<?=$identifica_concepto?> requerido save_ok" onfocus="" ></td><!-- crearTipoCobroSsn('<?=$identifica_concepto?>','cobro_hora','<?=$nombre_concepto?>','<?=$wprocedimiento?>','<?=$nombre_procedimiento?>'); -->
                    </tr>
                    <tr>
                        <td class="fila1">Cobro por hora</td>
                        <td class="fila2">
                            <input concepto="<?=$identifica_concepto?>" type="checkbox" id="wcobro_por_hora_<?=$identifica_concepto?>" name="wcobro_por_hora_<?=$identifica_concepto?>" value="on" class="cobroHora_<?=$identifica_concepto?> save_ok" <?=$default_es_cobro_horas?> onfocus="" ></td><!-- crearTipoCobroSsn('<?=$identifica_concepto?>','cobro_hora','<?=$nombre_concepto?>','<?=$wprocedimiento?>','<?=$nombre_procedimiento?>'); -->
                    </tr>
                    <tr>
                        <td class="fila1"><?=CONCEPTO_LABEL?></td>
                        <td class="fila2" >
                            <input concepto="<?=$identifica_concepto?>" type="text" id="wconcepto_por_tiempo_<?=$identifica_concepto?>" name="wconcepto_por_tiempo_<?=$identifica_concepto?>" value="" codigo="" nombre="" placeholder="Concepto" size="30" class="cobroHora_<?=$identifica_concepto?> requerido save_ok" onfocus="" ><!-- crearTipoCobroSsn('<?=$identifica_concepto?>','cobro_hora','<?=$nombre_concepto?>','<?=$wprocedimiento?>','<?=$nombre_procedimiento?>'); -->
                        </td>
                    </tr>
                    <tr>
                        <td class="fila1"><?=PROCEDIMIENTO?></td>
                        <td class="fila2" >
                            <input concepto="<?=$identifica_concepto?>" type="text" id="wprocedimiento_por_tiempo_<?=$identifica_concepto?>" name="wprocedimiento_por_tiempo_<?=$identifica_concepto?>" value="" codigo="" nombre="" placeholder="Procedimiento" size="30" class="cobroHora_<?=$identifica_concepto?> requerido save_ok" onfocus="" ><!-- crearTipoCobroSsn('<?=$identifica_concepto?>','cobro_hora','<?=$nombre_concepto?>','<?=$wprocedimiento?>','<?=$nombre_procedimiento?>'); -->
                        </td>
                    </tr>
                </table>
            </div>
            <!-- CONTENIDO COBRO POR HORAS -->

            <!-- CONTENIDO POR ANESTESIA -->
            <div id="tabr2_<?=$identifica_concepto?>" class="tab-content">
                <table align="center" id="tabla_cont_rangos_anestesia_<?=$identifica_concepto?>">
                    <tr>
                        <td>
                            <table align="center">
                                <tr class="encabezadoTabla">
                                    <td align="center"><?=ANESTESIA?></td>
                                    <td align="center"><?=TIEMPO_INICIAL_MTS?></td>
                                    <td align="center"><?=TIEMPO_FINAL_MTS?></td>
                                    <td align="center"><?=CONCEPTO_LABEL?></td>
                                    <td align="center"><?=PROCEDIMIENTO?></td>
                                    <td align="center">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td align="center" class="fila2">
                                        <select class="requerido" id="wtipo_anestesia_<?=$identifica_concepto?>" name="wtipo_anestesia_<?=$identifica_concepto?>">
                                            <option value="">Seleccione..</option>
                                            <?php
                                            foreach ($arr_tipo_anestesia as $key => $value) {
                                                echo '<option value="'.$key.'">'.utf8_encode($value).'</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td align="center" class="fila2"><input type="text" id="wtiempo_inicial_anest_<?=$identifica_concepto?>" name="wtiempo_inicial_anest_<?=$identifica_concepto?>" value="" class="requerido numerico" placeholder="Minutos" maxlength="" size="8"></td>
                                    <td align="center" class="fila2"><input type="text" id="wtiempofinal_anest_<?=$identifica_concepto?>" name="wtiempofinal_anest_<?=$identifica_concepto?>" value="" class="requerido numerico" placeholder="Minutos" size="8"></td>
                                    <td align="center" class="fila2"><input type="text" id="wcodigo_concepto_anest_<?=$identifica_concepto?>" name="wcodigo_concepto_anest_<?=$identifica_concepto?>" value="" class="requerido" placeholder="<?=CONCEPTO_LABEL?>" maxlength="" size=""></td>
                                    <td align="center" class="fila2"><input type="text" id="wcodigo_procedimiento_anest_<?=$identifica_concepto?>" name="wcodigo_procedimiento_anest_<?=$identifica_concepto?>" value="" class="requerido" placeholder="<?=PROCEDIMIENTO?>" maxlength="" size=""></td>
                                    <td align="center" class="">
                                        <button onclick="javascript: nuevoRangoAnestesia('tabla_rangos_anestesia_<?=$identifica_concepto?>','<?=$identifica_concepto?>');">Agregar rango</button>
                                    </td><!-- crearTipoCobroSsn('<?=$identifica_concepto?>','cobro_anestesia','<?=$nombre_concepto?>','<?=$wprocedimiento?>','<?=$nombre_procedimiento?>'); -->
                                </tr>
                            </table>
                            <hr>
                            <table align="center" id="tabla_rangos_anestesia_<?=$identifica_concepto?>">
                                <tr class="encabezadoTabla">
                                    <td align="center"><?=ANESTESIA?></td>
                                    <td align="center"><?=TIEMPO_INICIAL_MTS?></td>
                                    <td align="center"><?=TIEMPO_FINAL_MTS?></td>
                                    <td align="center"><?=CONCEPTO_LABEL?></td>
                                    <td align="center"><?=PROCEDIMIENTO?></td>
                                    <td align="center">&nbsp;</td>
                                </tr>
                                <?=(($tipo_cobro=='cobro_anestesia') ? $html_filas_configuradas: '')?>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
            <!-- CONTENIDO POR ANESTESIA -->

            <!-- CONTENIDO POR RANGOS -->
            <div id="tabr3_<?=$identifica_concepto?>" class="tab-content">
                <table align="center" id="tabla_rangos_uso_<?=$identifica_concepto?>">
                    <tr>
                        <td><br>							
                            <table align="center" >
								<tr class="fila1">
									<td colspan="5" align="center">
										El tiempo que se va a tener en cuenta para aplicar el rango es:
										<select tiempo_Aplicar concepto='<?=$identifica_concepto?>'>
											<option value="off">Tiempo total de la cx</option>
											<option value="on">Tiempo de cada procedimiento</option>
										</select>
									</td>
								</tr>
                                <tr class="encabezadoTabla">
                                    <td align="center"><?=TIEMPO_INICIAL_MTS?></td>
                                    <td align="center"><?=TIEMPO_FINAL_MTS?></td>
                                    <td align="center"><?=CONCEPTO_LABEL?></td>
                                    <td align="center"><?=PROCEDIMIENTO?></td>
                                    <td align="center">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td align="center" class="fila2"><input type="text" id="wtiempo_inicial_por_rangos_<?=$identifica_concepto?>" name="wtiempo_inicial_por_rangos_<?=$identifica_concepto?>" value="" class="requerido numerico" placeholder="Minutos" maxlength="" size="8"></td>
                                    <td align="center" class="fila2"><input type="text" id="wtiempo_final_por_rangos_<?=$identifica_concepto?>" name="wtiempo_final_por_rangos_<?=$identifica_concepto?>" value="" class="requerido numerico" placeholder="Minutos" maxlength="" size="8"></td>
                                    <td align="center" class="fila2"><input type="text" id="wcodigo_concepto_rango_por_rango_<?=$identifica_concepto?>" name="wcodigo_concepto_rango_por_rango_<?=$identifica_concepto?>" value="" class="requerido" placeholder="<?=CONCEPTO_LABEL?>" maxlength="" size=""></td>
                                    <td align="center" class="fila2"><input type="text" id="wcodigo_procedimiento_rango_por_rango_<?=$identifica_concepto?>" name="wcodigo_procedimiento_rango_por_rango_<?=$identifica_concepto?>" value="" class="requerido" placeholder="<?=PROCEDIMIENTO?>" maxlength="" size=""></td>
                                    <td align="center" class="fila2">
                                        <button onclick="javascript: nuevoRangoPorRangos('tabla_rangos_por_rango_<?=$identifica_concepto?>','<?=$identifica_concepto?>')">Agregar rango</button>
                                    </td><!-- crearTipoCobroSsn('<?=$identifica_concepto?>','cobro_uso','<?=$nombre_concepto?>','<?=$wprocedimiento?>','<?=$nombre_procedimiento?>'); -->
                                </tr>
                            </table>
                            <hr>
                            <table align="center" id="tabla_rangos_por_rango_<?=$identifica_concepto?>">
                                <tr class="encabezadoTabla">
                                    <td align="center"><?=TIEMPO_INICIAL_MTS?></td>
                                    <td align="center"><?=TIEMPO_FINAL_MTS?></td>
                                    <td align="center"><?=CONCEPTO_LABEL?></td>
                                    <td align="center"><?=PROCEDIMIENTO?></td>
                                    <td align="center">&nbsp;</td>
                                </tr>
                                <?=(($tipo_cobro=='cobro_uso') ? $html_filas_configuradas: '')?>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
            <!-- CONTENIDO POR RANGOS -->
            <!-- CONTENIDO DE LAS PESTAÑAS -->
        </fieldset>
        <?php
        }
        else
        {
        ?>
        <div id="contenedor_inventario_<?=$identifica_concepto?>"></div>
        <?php
        }
        ?>

        <!-- <div id="div_excepcion_<?=$identifica_concepto?>" class="fila1" >Excepciones para UVR's</div>
        <div id="div_ver_excepcion_<?=$identifica_concepto?>">
            <table align="center" id="tabla_crear_limite_<?=$identifica_concepto?>">
                <tr class="encabezadoTabla">
                    <td>Límite inferior UVR (inc)</td>
                    <td>Límite superior UVR (inc)</td>
                    <td>Procedimiento a cobrar</td>
                    <td>&nbsp;</td>
                </tr>
                <tr class="fila1">
                    <td style="text-align:center;"><input type="text" id="limit_inf_<?=$identifica_concepto?>" name="limit_inf_<?=$identifica_concepto?>" value ="" placeholder="0" size="5" class="requerido numerico"></td>
                    <td style="text-align:center;"><input type="text" id="limit_sup_<?=$identifica_concepto?>" name="limit_sup_<?=$identifica_concepto?>" value ="" placeholder="0" size="5" class="requerido numerico"></td>
                    <td style="text-align:center;"><input concepto="<?=$identifica_concepto?>" type="text" id="wprocedimiento_limit_<?=$identifica_concepto?>" name="wprocedimiento_limit_<?=$identifica_concepto?>" class="requerido" value="" codigo="" nombre="" placeholder="Procedimiento" size="30" class="requerido save_ok" onfocus="" ></td>
                    <td><button onclick="javascript: nuevoLimite('tabla_limites_<?=$identifica_concepto?>','<?=$identifica_concepto?>','<?=$wnuevo_concepto_gral?>','<?=$nombre_concepto?>')">Agregar límite</button></td>
                </tr>
            </table>

            <table style="margin-top:5px;" align="center" id="tabla_limites_<?=$identifica_concepto?>">
                <tr class="encabezadoTabla">
                    <td>Límite inferior UVR (inc)</td>
                    <td>Límite superior UVR (inc)</td>
                    <td>Procedimiento a cobrar</td>
                    <td>No facturable</td>
                    <td>&nbsp;</td>
                </tr>
                <?=$html_filas_limites?>
            </table>

        </div> -->
    </div>
</div>
<!-- CONCEPTOS PLANTILLA -->