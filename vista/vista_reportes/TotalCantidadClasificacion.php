<?php
/**
 *@package pXP
 *@file    KardexItem.php
 *@author  RCM
 *@date    06/07/2013
 *@description Archivo con la interfaz para generación de reporte
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.TotalCantidadClasificacion = Ext.extend(Phx.frmInterfaz, {

        constructor: function(config) {
            Ext.apply(this,config);
            this.Atributos = [
                
                {
                    config : {
                        name : 'fecha_ini',
                        //id:'fecha_ini'+this.idContenedor,
                        fieldLabel : 'Fecha Desde',
                        allowBlank : false,
                        gwidth : 100,
                        format : 'd/m/Y',
                        renderer : function(value, p, record) {
                            return value ? value.dateFormat('d/m/Y h:i:s') : ''
                        },
                        vtype: 'daterange',
                        //endDateField: 'fecha_fin'+this.idContenedor
                    },
                    type : 'DateField',
                    id_grupo : 0,
                    grid : true,
                    form : true
                },
                {
                    config : {
                        name : 'fecha_hasta',
                        //id:'fecha_fin'+this.idContenedor,
                        fieldLabel: 'Fecha Hasta',
                        allowBlank: false,
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer: function(value, p, record) {
                            return value ? value.dateFormat('d/m/Y h:i:s') : ''
                        },
                        vtype: 'daterange',
                        //startDateField: 'fecha_ini'+this.idContenedor
                    },
                    type : 'DateField',
                    id_grupo : 0,
                    grid : true,
                    form : true
                },
                {
                    config: {
                        name: 'id_clasificacion',
                        fieldLabel: 'Clasificación',
                        allowBlank: false,
                        emptyText: 'Elija una clasificación...',
                        store : new Ext.data.JsonStore({
                            url : '../../sis_almacenes/control/Clasificacion/listarClasificacion',
                            id : 'id_clasificacion',
                            root : 'datos',
                            sortInfo : {
                                field : 'codigo_largo',
                                direction : 'ASC'
                            },
                            totalProperty : 'total',
                            fields : ['id_clasificacion', 'nombre', 'codigo_largo'],
                            remoteSort : true,
                            baseParams : {
                                par_filtro : 'cla.nombre#cla.codigo_largo', primer_nivel : 'si'
                            }
                        }),
                        //hidden: true,
                        valueField: 'id_clasificacion',
                        displayField: 'nombre',
                        gdisplayField: 'nombre',
                        forceSelection: false,
                        typeAhead: false,
                        triggerAction: 'all',
                        //tpl : '<tpl for="."><div class="x-combo-list-item"><p style="color: green">Nombre: {nombre}</p><p>Clasificación: {codigo_largo}</p></div></tpl>',
                        tpl: new Ext.XTemplate([
                            '<tpl for=".">',
                            '<div class="x-combo-list-item">',
                            '<div class="awesomecombo-item {checked}">',
                            '<p><b>Clasificación: {codigo_largo}</b></p>',
                            '</div><p><b>Nombre: </b> <span style="color: green;">{nombre}</span></p>',
                            '</div></tpl>'
                        ]),
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 20,
                        queryDelay: 500,
                        anchor: '99%',
                        gwidth: 150,
                        minChars: 2,
                        width:350,
                        listWidth: 400,
                        enableMultiSelect: true,
                        renderer: function (value, p, record) {
                            return String.format('{0}', value?record.data['nombre']:'');
                        }
                    },
                    type: 'AwesomeCombo',
                    id_grupo: 0,
                    grid: true,
                    form: true
                },
                {
                    config : {
                        name : 'all_items',
                        fieldLabel : 'Todos los Almacenes',
                        allowBlank : false,
                        triggerAction : 'all',
                        lazyRender : true,
                        mode : 'local',
                        store : new Ext.data.ArrayStore({
                            fields : ['codigo', 'nombre'],
                            data : [['si', 'Si'], ['no', 'No']]
                        }),
                        anchor : '50%',
                        valueField : 'codigo',
                        displayField : 'nombre'
                    },
                    type : 'ComboBox',
                    id_grupo : 1,
                    form : true
                },
                {
                    config : {
                        name : 'id_almacen',
                        fieldLabel : 'Almacenes',
                        allowBlank : true,
                        emptyText : 'Almacenes...',
                        store : new Ext.data.JsonStore({
                            url : '../../sis_almacenes/control/Almacen/listarAlmacen',
                            id : 'id_almacen',
                            root : 'datos',
                            sortInfo : {
                                field : 'nombre',
                                direction : 'ASC'
                            },
                            totalProperty : 'total',
                            fields : ['id_almacen', 'nombre','codigo'],
                            remoteSort : true,
                            baseParams : {
                                par_filtro: 'alm.nombre#alm.codigo'
                            }
                        }),
                        valueField : 'id_almacen',
                        //hiddenValue: 'id_item',
                        displayField : 'nombre',
                        gdisplayField : 'nombre',
                        //tpl : '<tpl for="."><div class="x-combo-list-item"><p>Nombre: {nombre}</p><p>Código: {codigo}</p><p>Clasif.: {desc_clasificacion}</p></div></tpl>',
                        hiddenName : 'id_almacen',
                        forceSelection : true,
                        typeAhead : false,
                        triggerAction : 'all',
                        lazyRender : true,
                        mode : 'remote',
                        pageSize : 10,
                        queryDelay : 1000,
                        anchor : '100%',
                        gwidth : 250,
                        minChars : 2,
                        renderer : function(value, p, record) {
                            return String.format('{0}', record.data['nombre']);
                        },
                        enableMultiSelect : true
                    },
                    type : 'AwesomeCombo',
                    id_grupo : 0,
                    grid : false,
                    form : true
                }];

            Phx.vista.TotalCantidadClasificacion.superclass.constructor.call(this, config);
            this.init();

            this.getComponente('all_items').on('select', function(e, component, index) {
                var cmbAlm = this.getComponente('id_almacen');
                if (e.value == 'si') {
                    cmbAlm.disable();
                    cmbAlm.allowBlank=true;
                } else {
                    cmbAlm.enable();
                    cmbAlm.allowBlank=false;
                }
            }, this);

            this.getComponente('all_items').setValue('si');
            this.getComponente('id_almacen').disable();

            //Eventos
            this.getComponente('id_clasificacion').on('select',function(a,b,c){
                this.desc_item = b.data.codigo+' - '+b.data.nombre;
            },this);
        },
        title : 'Cantidades x Clasificación',
        ActSave : '../../sis_almacenes/control/Reportes/listarTotalCantidadesClasificacion',
        topBar : true,
        botones : false,
        labelSubmit : 'Generar',
        tooltipSubmit : '<b>Generar Reporte Cantidades x Clasificación</b>',
        tipo : 'reporte',
        clsSubmit : 'bprint',
        Grupos : [{
            layout : 'column',
            items : [{
                xtype : 'fieldset',
                layout : 'form',
                border : true,
                title : 'Generar Reporte',
                bodyStyle : 'padding:0 10px 0;',
                columnWidth : '300px',
                items : [],
                id_grupo : 0,
                collapsible : true
            }]
        }],
        desc_item:''

    })
</script>