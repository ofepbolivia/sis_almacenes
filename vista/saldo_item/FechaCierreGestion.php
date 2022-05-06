<?php
/**
 *@package pXP
 *@file    ReporteGlobalAF.php
 *@author  Franklin Espinoza Alvarez
 *@date    23-01-2018
 *@description Archivo con la interfaz para generación de reporte
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.FechaCierreGestion = Ext.extend(Phx.frmInterfaz, {

        //fwidth: '100%',
        constructor : function(config) {
            Phx.vista.FechaCierreGestion.superclass.constructor.call(this, config);
            this.init();
            this.iniciarEventos();
        },

        Atributos : [
            {
                config:{
                    name: 'fecha_cierre',
                    fieldLabel: 'Fecha Cierre',
                    allowBlank: false,
                    qtip: 'Esta fecha se tomara como base para el cierre de Gestión',
                    width: 177,

                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                },
                type:'DateField',
                //filters:{pfiltro:'sal.fecha_hasta',type:'date'},
                id_grupo:0,
                grid:true,
                form:true
            }
        ],
        title : 'Fecha Cierre Gestión',
        ActSave : '../../sis_almacenes/control/Item/actualizarSaldoFisicoItem',
        successSave:function(resp){
            Phx.CP.loadingHide();
            Phx.CP.getPagina(this.idContenedorPadre).reload();
            this.panel.close();
        }
    });
</script>