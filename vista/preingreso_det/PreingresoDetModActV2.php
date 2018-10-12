<?php
/**
 *@package pXP
 *@file PreingresoDetModActV2.php
 *@author  RCM
 *@date 08/08/2017
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.PreingresoDetModActV2=Ext.extend(Phx.gridInterfaz,{
        estado: 'mod',
        constructor:function(config){
            this.maestro=config.maestro;
            //llama al constructor de la clase padre
            Phx.vista.PreingresoDetModActV2.superclass.constructor.call(this,config);
            this.grid.getTopToolbar().disable();
            this.grid.getBottomToolbar().disable();
            this.grid.addListener('cellclick', this.oncellclick,this);
            this.init();



            //Se agrega el botón para adicionar todos
            this.addButton('btnAgTodos', {
                text : 'Quitar Todos',
                iconCls : 'bleft-all',
                disabled : true,
                handler : this.quitarTodos,
                tooltip : '<b>Quitar Todos</b><br/>Quita todos los items del preingreso.'
            });

            /*this.detailsTemplate = new Ext.XTemplate(
                '<div class="details">',
                    '<tpl for=".">',
                        '<div class="details-info">',
                        '<h1>Preingreso:</h1>',
                        '<br><center><span>{nombre}</span></center>',
                        '</div>',
                    '</tpl>',
                '</div>'
            );

            this.detailsTemplate.compile();*/
        },



        Atributos:[
            {
                //configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_preingreso_det'
                },
                type:'Field',
                form:true
            },
            {
                config:{
                    labelSeparator:'',
                    //inputType:'hidden',
                    name: 'id_preingreso'
                },
                type:'Field',
                form:true
            },
            {
                config:{
                    name: 'quitar',
                    fieldLabel: 'Quitar',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 50,
                    scope: this,
                    renderer:function (value, p, record, rowIndex, colIndex){
                        return "<div style='text-align:center'><img border='0' style='-webkit-user-select:auto;cursor:pointer;' title='Quitar' src = '../../../lib/imagenes/icono_awesome/awe_left_arrow.png' align='center' width='30' height='30'></div>";
                    }
                },
                type:'Checkbox',
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'agregar',
                    fieldLabel: 'Incluido',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 50,
                    scope: this,
                    renderer:function (value, p, record, rowIndex, colIndex){
                        return "<div style='text-align:center'><img border='0' style='-webkit-user-select:auto;cursor:pointer;' src = '../../../lib/imagenes/icono_awesome/awe_ok.png' align='center' width='30' height='30'></div>";
                    }
                },
                type:'Checkbox',
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'cantidad_det',
                    fieldLabel: 'Cantidad',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 55,
                    maxLength:200
                },
                type:'NumberField',
                filters:{pfiltro:'predet.cantidad_det',type:'numeric'},
                id_grupo:1,
                grid:true,
                form:true
            },

            {
                config: {
                    name: 'id_unidad_medida',
                    fieldLabel: 'Unidad de Medida',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 150,
                    maxLength: 50,
                    renderer: function(value, p, record) {
                        return String.format('{0}', record.data['descripcion_unmed']);
                    }
                },
                type: 'TextField',
                filters: {
                    pfiltro: 'predet.id_unidad_medida',
                    type: 'string'
                },
                id_grupo: 1,
                grid: true,
                form: true,
                bottom_filter:true
            },
            {
                config: {
                    name: 'id_cat_estado_fun',
                    fieldLabel: 'id_cat_estado_fun',
                    allowBlank: true,
                    emptyText: 'Elija una opción...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_/control/Clase/Metodo',
                        id: 'id_',
                        root: 'datos',
                        sortInfo: {
                            field: 'codigo',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_', 'nombre', 'codigo'],
                        remoteSort: true,
                        baseParams: {
                            par_filtro: 'movtip.nombre#movtip.codigo'
                        }
                    }),
                    valueField: 'id_',
                    displayField: 'nombre',
                    gdisplayField: 'descripcion',
                    hiddenName: 'id_cat_estado_fun',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    anchor: '100%',
                    gwidth: 150,
                    minChars: 2,
                    renderer: function(value, p, record) {
                        return String.format('{0}', record.data['descripcion']);
                    }
                },
                type: 'ComboBox',
                id_grupo: 0,
                filters: {
                    pfiltro: 'movtip.nombre',
                    type: 'string'
                },
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'subtipo',
                    fieldLabel: 'Subtipo',
                    allowBlank: true,
                    emptyText: 'Elija una opción...',
                    store: new Ext.data.ArrayStore({
                           id: 0,
                           fields: [
                               'myId',
                               'displayText'
                           ],
                           data: [[1, 'item1'], [2, 'item2']]
                       }),
                       valueField: 'myId',
                       displayField: 'displayText'
                    },
                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode: 'local' ,
                    type: 'ComboBox',
                    id_grupo: 0,
                    filters: {
                        pfiltro: 'movtip.nombre',
                        type: 'string'
                    },
                    grid: true,
                    form: true
            },
            {
                config: {
                    name: 'movimiento',
                    fieldLabel: 'Tipo de Movimiento',
                    allowBlank: true,
                    emptyText: 'Elija una opción...',
                    store: new Ext.data.ArrayStore({
                           id: 0,
                           fields: [
                               'myId',
                               'displayText'
                           ],
                           data: [[1, 'item1'], [2, 'item2']]
                       }),
                       valueField: 'myId',
                       displayField: 'displayText'
                    },
                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode: 'local' ,
                    type: 'ComboBox',
                    id_grupo: 0,
                    filters: {
                        pfiltro: 'movtip.nombre',
                        type: 'string'
                    },
                    grid: true,
                    form: true
            },
            {
                config:{
                    name: 'precio_compra',
                    fieldLabel: 'Costo al 100%',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 75,
                    maxLength:1179650
                },
                type:'NumberField',
                filters:{pfiltro:'predet.precio_compra',type:'numeric'},
                id_grupo:1,
                grid:true,
                form:true
            },

            {
                config:{
                    name: 'precio_compra_87',
                    fieldLabel: 'Costo',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 75,
                    maxLength:1179650
                },
                type:'NumberField',
                filters:{pfiltro:'predet.precio_compra_87',type:'numeric'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config : {
                    name : 'id_item',
                    fieldLabel : 'Item',
                    allowBlank : false,
                    emptyText : 'Elija un Item...',
                    store : new Ext.data.JsonStore({
                        url : '../../sis_almacenes/control/Item/listarItemNotBase',
                        id : 'id_item',
                        root : 'datos',
                        sortInfo : {
                            field : 'nombre',
                            direction : 'ASC'
                        },
                        totalProperty : 'total',
                        fields : ['id_item', 'nombre', 'codigo', 'desc_clasificacion', 'codigo_unidad'],
                        remoteSort : true,
                        baseParams : {
                            par_filtro : 'item.nombre#item.codigo#cla.nombre'
                        }
                    }),
                    valueField : 'id_item',
                    displayField : 'nombre',
                    gdisplayField : 'desc_item',
                    tpl : '<tpl for="."><div class="x-combo-list-item"><p>Nombre: {nombre}</p><p>Código: {codigo}</p><p>Clasif.: {desc_clasificacion}</p></div></tpl>',
                    hiddenName : 'id_item',
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
                    turl : '../../../sis_almacenes/vista/item/BuscarItem.php',
                    tasignacion : true,
                    tname : 'id_item',
                    ttitle : 'Items',
                    tdata : {},
                    tcls : 'BuscarItem',
                    pid : this.idContenedor,
                    renderer : function(value, p, record) {
                        return String.format('{0}', record.data['desc_item']);
                    },
                    resizable: true
                },
                type : 'TrigguerCombo',
                id_grupo : 0,
                filters : {
                    pfiltro : 'item.nombre',
                    type : 'string'
                },
                grid : true,
                form : true
            },
            {
                config : {
                    name : 'id_almacen',
                    fieldLabel : 'Almacén',
                    allowBlank : false,
                    emptyText : 'Almacén...',
                    store : new Ext.data.JsonStore({
                        url : '../../sis_almacenes/control/Almacen/listarAlmacen',
                        id : 'id_almacen',
                        root : 'datos',
                        sortInfo : {
                            field : 'nombre',
                            direction : 'ASC'
                        },
                        totalProperty : 'total',
                        fields : ['id_almacen', 'nombre'],
                        remoteSort : true,
                        baseParams : {
                            par_filtro : 'alm.nombre'
                        }
                    }),
                    valueField : 'id_almacen',
                    displayField : 'nombre',
                    gdisplayField : 'desc_almacen',
                    hiddenName : 'id_almacen',
                    forceSelection : true,
                    typeAhead : false,
                    triggerAction : 'all',
                    lazyRender : true,
                    mode : 'remote',
                    pageSize : 10,
                    queryDelay : 1000,
                    anchor : '100%',
                    gwidth : 150,
                    minChars : 2,
                    renderer : function(value, p, record) {
                        return String.format('{0}', record.data['desc_almacen']);
                    }
                },
                type : 'ComboBox',
                id_grupo : 0,
                filters : {
                    pfiltro : 'alm.codigo',
                    type : 'string'
                },
                grid : true,
                form : true
            },
            {
                config: {
                    name: 'id_clasificacion',
                    fieldLabel: 'Clasificación',
                    allowBlank: false,
                    emptyText: 'Elija una opción...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_kactivos_fijos/control/Clasificacion/ListarClasificacionTree',
                        id: 'id_clasificacion',
                        root: 'datos',
                        sortInfo: {
                            field: 'orden',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_clasificacion','clasificacion', 'id_clasificacion_fk','tipo_activo','depreciable','vida_util',''],
                        remoteSort: true,
                        baseParams: {
                            par_filtro:'claf.clasificacion'
                        }
                    }),
                    valueField: 'id_clasificacion',
                    displayField: 'clasificacion',
                    gdisplayField: 'desc_clasificacion',
                    hiddenName: 'id_clasificacion',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    anchor: '100%',
                    gwidth: 150,
                    minChars: 2
                },
                type: 'ComboBox',
                id_grupo: 0,
                filters: {pfiltro: 'claf.clasificacion',type: 'string'},
                grid: true,
                form: true
            },
            {
                config:{
                    name: 'vida_util_original',
                    fieldLabel: 'Vida Util Original',
                    allowBlank: false,
                    anchor: '100%',
                    gwidth: 180,
                    maxLength:255
                },
                type:'TextField',
                filters:{pfiltro:'predet.vida_util_original',type:'numeric'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config: {
                    name: 'nro_serie',
                    fieldLabel: '# Serie',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 130,
                    maxLength: 50
                },
                type: 'TextField',
                filters: {
                    pfiltro: 'predet.nro_serie',
                    type: 'string'
                },
                id_grupo: 1,
                grid: true,
                form: true,
                bottom_filter:true
            },
            {
                config: {
                    name: 'marca',
                    fieldLabel: 'Marca',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 150,
                    maxLength: 50
                },
                type: 'TextField',
                filters: {
                    pfiltro: 'predet.marca',
                    type: 'string'
                },
                id_grupo: 1,
                grid: true,
                form: true,
                bottom_filter:true
            },
            {
                config: {
                    name: 'id_depto',
                    fieldLabel: 'Depto.',
                    allowBlank: false,
                    emptyText: 'Elija una opción...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_parametros/control/Depto/listarDeptoFiltradoDeptoUsuario',
                        id: 'id_',
                        root: 'datos',
                        sortInfo: {
                            field: 'nombre',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_depto', 'codigo', 'nombre'],
                        remoteSort: true,
                        baseParams: {par_filtro: 'DEPPTO.nombre#DEPPTO.codigo'}
                    }),
                    valueField: 'id_depto',
                    displayField: 'nombre',
                    gdisplayField: 'desc_depto',
                    hiddenName: 'id_depto',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    anchor: '100%',
                    gwidth: 150,
                    minChars: 2,
                    renderer : function(value, p, record) {
                        return String.format('{0}', record.data['desc_depto']);
                    },
                    listWidth:300
                },
                type: 'ComboBox',
                id_grupo: 5,
                filters: {pfiltro: 'depto.nombre',type: 'string'},
                grid: true,
                form: true
            },
            {
                config:{
                    name: 'nombre',
                    fieldLabel: 'Nombre',
                    allowBlank: false,
                    anchor: '100%',
                    gwidth: 180,
                    maxLength:100
                },
                type:'TextArea',
                filters:{pfiltro:'predet.nombre',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'descripcion',
                    fieldLabel: 'Descripción',
                    allowBlank: true,
                    anchor: '100%',
                    gwidth: 300,
                    maxLength:2000
                },
                type:'TextArea',
                filters:{pfiltro:'predet.descripcion',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'id_lugar',
                    fieldLabel: 'Lugar',
                    allowBlank: false,
                    emptyText:'Lugar...',
                    store:new Ext.data.JsonStore(
                        {
                            url: '../../sis_parametros/control/Lugar/listarLugar',
                            id: 'id_lugar',
                            root: 'datos',
                            sortInfo:{
                                field: 'nombre',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_lugar','id_lugar_fk','codigo','nombre','tipo','sw_municipio','sw_impuesto','codigo_largo'],
                            // turn on remote sorting
                            remoteSort: true,
                            baseParams:{par_filtro:'lug.nombre',tipo:'departamento'}
                        }),
                    valueField: 'id_lugar',
                    displayField: 'nombre',
                    gdisplayField:'nombre_lugar',
                    hiddenName: 'id_lugar',
                    triggerAction: 'all',
                    lazyRender:true,
                    mode:'remote',
                    pageSize:50,
                    queryDelay:500,
                    anchor:"100%",
                    gwidth:150,
                    minChars:2,
                    renderer:function (value, p, record){return String.format('{0}', record.data['nombre_lugar']);}
                },
                type:'ComboBox',
                filters:{pfiltro:'lug.nombre',type:'string'},
                id_grupo:0,
                grid:true,
                form:true
            },
            {
                config: {
                    name: 'id_deposito',
                    fieldLabel: 'Depósito',
                    allowBlank: true,
                    emptyText: 'Elija una opción...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_/control/Clase/Metodo',
                        id: 'id_',
                        root: 'datos',
                        sortInfo: {
                            field: 'nombre',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_', 'nombre', 'codigo'],
                        remoteSort: true,
                        baseParams: {
                            par_filtro: 'movtip.nombre#movtip.codigo'
                        }
                    }),
                    valueField: 'id_',
                    displayField: 'nombre',
                    gdisplayField: 'desc_',
                    hiddenName: 'id_deposito',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    anchor: '100%',
                    gwidth: 150,
                    minChars: 2,
                    renderer: function(value, p, record) {
                        return String.format('{0}', record.data['deposito']);
                    }
                },
                type: 'ComboBox',
                id_grupo: 0,
                filters: {
                    pfiltro: 'movtip.nombre',
                    type: 'string'
                },
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'id_oficina',
                    fieldLabel: 'Oficina',
                    allowBlank: true,
                    emptyText: 'Elija una opción...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_/control/Clase/Metodo',
                        id: 'id_',
                        root: 'datos',
                        sortInfo: {
                            field: 'nombre',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_', 'nombre', 'codigo'],
                        remoteSort: true,
                        baseParams: {
                            par_filtro: 'movtip.nombre#movtip.codigo'
                        }
                    }),
                    valueField: 'id_',
                    displayField: 'nombre',
                    gdisplayField: 'desc_',
                    hiddenName: 'id_oficina',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    anchor: '100%',
                    gwidth: 150,
                    minChars: 2,
                    renderer: function(value, p, record) {
                        return String.format('{0}', record.data['oficina']);
                    }
                },
                type: 'ComboBox',
                id_grupo: 0,
                filters: {
                    pfiltro: 'movtip.nombre',
                    type: 'string'
                },
                grid: true,
                form: true
            },
            {
                config:{
                    name: 'ubicacion',
                    fieldLabel: 'Ubicación',
                    allowBlank: false,
                    anchor: '100%',
                    gwidth: 180,
                    maxLength:255
                },
                type:'TextArea',
                filters:{pfiltro:'predet.ubicacion',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config: {
                    name: 'id_proveedor',
                    fieldLabel: 'Proveedor',
                    allowBlank: true,
                    emptyText: 'Elija una opción...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_/control/Clase/Metodo',
                        id: 'id_',
                        root: 'datos',
                        sortInfo: {
                            field: 'nombre',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_', 'nombre', 'codigo'],
                        remoteSort: true,
                        baseParams: {
                            par_filtro: 'movtip.nombre#movtip.codigo'
                        }
                    }),
                    valueField: 'id_',
                    displayField: 'nombre',
                    gdisplayField: 'desc_',
                    hiddenName: 'id_proveedor',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    anchor: '100%',
                    gwidth: 150,
                    minChars: 2,
                    renderer: function(value, p, record) {
                        return String.format('{0}', record.data['desc_proveedor']);
                    }
                },
                type: 'ComboBox',
                id_grupo: 0,
                filters: {
                    pfiltro: 'movtip.nombre',
                    type: 'string'
                },
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'documento',
                    fieldLabel: 'Documento',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 100
                },
                type: 'TextField',
                filters: {
                    pfiltro: 'predet.documento',
                    type: 'string'
                },
                id_grupo: 1,
                grid: true,
                form: true,
                bottom_filter:true
            },

            {
                config:{
                    name: 'c31',
                    fieldLabel: 'C31',
                    allowBlank: false,
                    anchor: '100%',
                    gwidth: 180,
                    maxLength:255
                },
                type:'TextField',
                filters:{pfiltro:'predet.c31',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'tramite_compra',
                    fieldLabel: 'Nro. de Tramite de Compra',
                    allowBlank: false,
                    anchor: '100%',
                    gwidth: 180,
                    maxLength:255
                },
                type:'TextField',
                filters:{pfiltro:'predet.tramite_compra',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },

            {
                config:{
                    name: 'fecha_conformidad',
                    fieldLabel: 'Fecha Ini/Dep',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                },
                type:'DateField',
                filters:{pfiltro:'predet.fecha_conformidad',type:'date'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'fecha_compra',
                    fieldLabel: 'Fecha Compra(factura)',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                },
                type:'DateField',
                filters:{pfiltro:'predet.fecha_compra',type:'date'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config: {
                    name: 'id_cat_estado_compra',
                    fieldLabel: 'Estado Compra',
                    allowBlank: true,
                    emptyText: 'Elija una opción...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_/control/Clase/Metodo',
                        id: 'id_',
                        root: 'datos',
                        sortInfo: {
                            field: 'nombre',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_', 'nombre', 'codigo'],
                        remoteSort: true,
                        baseParams: {
                            par_filtro: 'movtip.nombre#movtip.codigo'
                        }
                    }),
                    valueField: 'id_',
                    displayField: 'nombre',
                    gdisplayField: 'desc_',
                    hiddenName: 'id_cat_estado_compra',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    anchor: '100%',
                    gwidth: 150,
                    minChars: 2,
                    renderer: function(value, p, record) {
                        return String.format('{0}', record.data['estado_compra']);
                    }
                },
                type: 'ComboBox',
                id_grupo: 0,
                filters: {
                    pfiltro: 'predet.id_cat_estado_compra',
                    type: 'string'
                },
                grid: true,
                form: true
            },

            {
                config: {
                    name: 'sw_generar',
                    fieldLabel: 'Generar',
                    anchor: '100%',
                    tinit: false,
                    allowBlank: true,
                    origen: 'CATALOGO',
                    gdisplayField: 'sw_generar',
                    gwidth: 100,
                    baseParams:{
                        cod_subsistema:'PARAM',
                        catalogo_tipo:'tgral__bandera_min'
                    },
                    renderer:function (value, p, record){return String.format('{0}', record.data['sw_generar']);}
                },
                type: 'ComboRec',
                id_grupo: 0,
                filters:{pfiltro:'predet.sw_generar',type:'string'},
                grid: false,
                form: false
            },
            {
                config:{
                    name: 'observaciones',
                    fieldLabel: 'Observaciones',
                    allowBlank: true,
                    anchor: '100%',
                    gwidth: 200,
                    maxLength:1000
                },
                type:'TextArea',
                filters:{pfiltro:'predet.observaciones',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config: {
                    name: 'monto_compra',
                    fieldLabel: 'Monto Vigente',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: -5
                },
                type: 'NumberField',
                filters: {
                    pfiltro: 'predet.monto_compra',
                    type: 'numeric'
                },
                id_grupo: 1,
                grid: true,
                form: true
            },
            {
                config:{
                    name: 'estado_reg',
                    fieldLabel: 'Estado Reg.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:10
                },
                type:'TextField',
                filters:{pfiltro:'predet.estado_reg',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'usr_reg',
                    fieldLabel: 'Creado por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:4
                },
                type:'NumberField',
                filters:{pfiltro:'usu1.cuenta',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'fecha_reg',
                    fieldLabel: 'Fecha creación',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                },
                type:'DateField',
                filters:{pfiltro:'predet.fecha_reg',type:'date'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'usr_mod',
                    fieldLabel: 'Modificado por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:4
                },
                type:'NumberField',
                filters:{pfiltro:'usu2.cuenta',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'fecha_mod',
                    fieldLabel: 'Fecha Modif.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                },
                type:'DateField',
                filters:{pfiltro:'predet.fecha_mod',type:'date'},
                id_grupo:1,
                grid:true,
                form:false
            }


        ],
        tam_pag:50,
        title:'Preingreso',
        ActSave:'../../sis_almacenes/control/PreingresoDet/insertarPreingresoDetPreparacion',
        ActDel:'../../sis_almacenes/control/PreingresoDet/eliminarPreingresoDetPreparacion',
        ActList:'../../sis_almacenes/control/PreingresoDet/listarPreingresoDetV2',
        id_store:'id_preingreso_det',
        fields: [
            {name:'id_preingreso_det', type: 'numeric'},
            {name:'estado_reg', type: 'string'},
            {name:'id_preingreso', type: 'numeric'},
            {name:'id_cotizacion_det', type: 'numeric'},
            {name:'id_item', type: 'numeric'},
            {name:'id_lugar', type: 'numeric'},
            {name:'id_almacen', type: 'numeric'},
            {name:'cantidad_det', type: 'numeric'},
            {name:'precio_compra', type: 'numeric'},
            {name:'precio_compra_87', type: 'numeric'},
            {name:'id_depto', type: 'numeric'},
            {name:'id_clasificacion', type: 'numeric'},
            {name:'sw_generar', type: 'string'},
            {name:'observaciones', type: 'string'},
            {name:'id_usuario_reg', type: 'numeric'},
            {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            {name:'id_usuario_mod', type: 'numeric'},
            {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            {name:'fecha_conformidad', type: 'date',dateFormat:'Y-m-d'},
            {name:'fecha_compra', type: 'date',dateFormat:'Y-m-d'},
            {name:'c31', type: 'string'},
            {name:'usr_reg', type: 'string'},
            {name:'usr_mod', type: 'string'},
            {name:'desc_almacen', type: 'string'},
            {name:'desc_depto', type: 'string'},
            {name:'desc_item', type: 'string'},
            {name:'desc_clasificacion', type: 'string'},
            {name:'nombre', type: 'string'},
            {name:'nombre_lugar', type: 'string'},
            {name:'ubicacion', type: 'string'},
            {name:'descripcion', type: 'string'},
            {name:'estado', type: 'string'},
            {name:'tipo', type: 'string'},
            {name:'id_unidad_medida', type: 'numeric'},
            {name:'codigo_unmed', type: 'string'},
            {name:'descripcion_unmed', type: 'string'},
            {name:'vida_util_original', type: 'numeric'},
            {name:'vida_util_original_anios', type: 'numeric'},
            {name:'nro_serie', type: 'string'},
            {name:'marca', type: 'string'},
            {name:'id_cat_estado_fun', type: 'numeric'},
            {name:'estado_fun', type: 'string'},
            {name:'id_deposito', type: 'numeric'},
            {name: 'deposito',type: 'string'},
            {name: 'id_oficina',type: 'numeric'},
            {name: 'oficina',type: 'string'},
            {name: 'id_proveedor',type: 'numeric'},
            {name: 'desc_proveedor',type: 'string'},
            {name: 'documento',type: 'string'},
            {name: 'id_cat_estado_compra',type: 'numeric'},
            {name: 'estado_compra',type: 'string'},
            {name:'fecha_cbte_asociado',type:'date',dateFormat: 'Y-m-d'},
            {name:'monto_compra',type: 'numeric'},
            {name:'id_proyecto',type: 'numeric'},
            {name:'desc_proyecto',type: 'string'},
            {name:'tramite_compra',type: 'string'},
            {name:'nombre_clasi',type: 'string'},
            {name:'subtipo',type: 'string'},
            {name:'movimiento',type: 'string'},


        ],
        sortInfo:{
            field: 'id_preingreso_det',
            direction: 'ASC'
        },
        bdel:false,
        bsave:false,
        bnew:false,
        bedit:true,

        bodyStyleForm: 'padding:5px;',
        borderForm: true,
        frameForm: false,
        paddingForm: '5 5 5 5',

        crearVentana: function() {
            if(this.afWindow){
                this.form.destroy();
                this.afWindow.destroy();
            }
                this.form = new Ext.form.FormPanel({
                    id: this.idContenedor,

                    items: [{
                        region: 'center',
                        layout: 'column',
                        border: false,
                        autoScroll: true,
                        items: [{
                            xtype: 'tabpanel',
                            plain: true,
                            activeTab: 0,
                            height: 515,
                            deferredRender: false,
                            defaults: {
                                bodyStyle: 'padding:10px'
                            },
                            items: [{
                                title: 'Principal',
                                layout: 'form',
                                defaults: {
                                    width: 400
                                },
                                autoScroll: true,
                                defaultType: 'textfield',
                                items: [{
                                    name: 'id_preingreso_det',
                                    hidden: true,
                                    id: this.idContenedor+'_id_preingreso_det'
                                },{
                                    name: 'id_preingreso',
                                    hidden: true,
                                    id: this.idContenedor+'_id_preingreso'
                                },
                                {
                                    name: 'id_item',
                                    hidden: true,
                                    id: this.idContenedor+'_id_item'
                                },
                                {
                                    name: 'id_almacen',
                                    hidden: true,
                                    id: this.idContenedor+'_id_almacen'
                                },
                                {
                                    xtype: 'combo',
                                    fieldLabel: 'Depto.',
                                    name: 'id_depto',
                                    allowBlank: false,
                                    id: this.idContenedor+'_id_depto',
                                    emptyText: 'Elija un Departamento',
                                    store: new Ext.data.JsonStore({
                                        url: '../../sis_parametros/control/Depto/listarDeptoFiltradoDeptoUsuario',
                                        id: 'id_',
                                        root: 'datos',
                                        fields: ['id_depto','codigo','nombre'],
                                        totalProperty: 'total',
                                        sortInfo: {
                                            field: 'codigo',
                                            direction: 'ASC'
                                        },
                                        baseParams:{
                                            start: 0,
                                            limit: 10,
                                            sort: 'codigo',
                                            dir: 'ASC',
                                            codigo_subsistema: 'KAF',
                                            par_filtro:'DEPPTO.codigo#DEPPTO.nombre'
                                        }
                                    }),
                                    valueField: 'id_depto',
                                    displayField: 'nombre',
                                    gdisplayField: 'desc_depto',
                                    mode: 'remote',
                                    triggerAction: 'all',
                                    lazyRender: true,
                                    pageSize: 15
                                }, {
                                    xtype: 'combo',
                                    fieldLabel: 'Clasificación',
                                    name: 'id_clasificacion',
                                    allowBlank: false,
                                    id: this.idContenedor+'_id_clasificacion',
                                    emptyText: 'Elija la Clasificación',
                                    store: new Ext.data.JsonStore({
                                        url: '../../sis_kactivos_fijos/control/Clasificacion/ListarClasificacionTree',
                                        id: 'id_clasificacion',
                                        root: 'datos',
                                        sortInfo: {
                                            field: 'orden',
                                            direction: 'ASC'
                                        },
                                        totalProperty: 'total',
                                        fields: ['id_clasificacion','clasificacion', 'id_clasificacion_fk','tipo_activo','depreciable','vida_util'],
                                        remoteSort: true,
                                        baseParams: {
                                            par_filtro:'claf.clasificacion'
                                        }
                                    }),
                                    valueField: 'id_clasificacion',
                                    displayField: 'clasificacion',
                                    gdisplayField: 'desc_clasificacion',
                                    hiddenName: 'id_clasificacion',
                                    mode: 'remote',
                                    triggerAction: 'all',
                                    typeAhead: false,
                                    lazyRender: true,
                                    pageSize: 15,
                                    queryDelay: 1000,
                                    minChars: 2
                                },{
                                    xtype: 'compositefield',
                                    fieldLabel: 'Vida útil inicial',
                                    items: [{
                                        xtype: 'label',
                                        text: 'Meses'
                                    }, {
                                        xtype: 'numberfield',
                                        fieldLabel: 'Vida útil inicial (meses)',
                                        name: 'vida_util_original',
                                        width: 60,
                                        allowBlank: true,
                                        id: this.idContenedor+'_vida_util_original'
                                    }, {
                                        xtype: 'label',
                                        text: 'Años'
                                    }, {
                                        xtype: 'numberfield',
                                        fieldLabel: 'Vida útil inicial (años)',
                                        name: 'vida_util_original_anios',
                                        width: 60,
                                        allowBlank: true,
                                        id: this.idContenedor+'_vida_util_original_anios'
                                    }]
                                },
                                {
                                    fieldLabel: '#Serie',
                                    name: 'nro_serie',
                                    allowBlank: true,
                                    id: this.idContenedor+'_nro_serie'
                                },
                                {
                                    fieldLabel: 'Marca',
                                    name: 'marca',
                                    allowBlank: true,
                                    id: this.idContenedor+'_marca'
                                },
                                {
                                    fieldLabel: 'Denominación',
                                    name: 'nombre',
                                    allowBlank: false,
                                    id: this.idContenedor+'_nombre'
                                }, {
                                    xtype: 'textarea',
                                    fieldLabel: 'Descripción',
                                    name: 'descripcion',
                                    allowBlank: false,
                                    id: this.idContenedor+'_descripcion'
                                }, {
                                    xtype: 'numberfield',
                                    fieldLabel: 'Cantidad',
                                    width: 60,
                                    name: 'cantidad_det',
                                    allowBlank: true,
                                    id: this.idContenedor+'_cantidad_det'
                                }, {
                                    xtype: 'combo',
                                    fieldLabel: 'Unidad de Medida',
                                    name: 'id_unidad_medida',
                                    //hiddenName: 'id_cat_estado_fun',
                                    allowBlank: true,
                                    id: this.idContenedor+'_id_unidad_medida',
                                    emptyText: 'Elija una opción',
                                    store: new Ext.data.JsonStore({
                                        url: '../../sis_parametros/control/UnidadMedida/listarUnidadMedida',
                                        id: 'id_unidad_medida',
                                        root: 'datos',
                                        fields: ['id_unidad_medida','codigo','descripcion'],
                                        totalProperty: 'total',
                                        sortInfo: {
                                            field: 'codigo',
                                            direction: 'ASC'
                                        },
                                        baseParams:{
                                            start: 0,
                                            limit: 10,
                                            sort: 'descripcion',
                                            dir: 'ASC'
                                        }
                                    }),
                                    valueField: 'id_unidad_medida',
                                    hiddenValue: 'id_unidad_medida',
                                    displayField: 'descripcion',
                                    gdisplayField: 'descripcion_unmed',
                                    mode: 'remote',
                                    triggerAction: 'all',
                                    lazyRender: true,
                                    pageSize: 15,
                                    tpl : '<tpl for="."><div class="x-combo-list-item"><p>{codigo} - {descripcion}</p></div></tpl>',
                                },{
                                    xtype: 'combo',
                                    fieldLabel: 'Estado funcional Actual',
                                    name: 'id_cat_estado_fun',
                                    //hiddenName: 'id_cat_estado_fun',
                                    allowBlank: false,
                                    id: this.idContenedor+'_id_cat_estado_fun',
                                    emptyText: 'Elija una opción',
                                    store: new Ext.data.JsonStore({
                                        url: '../../sis_parametros/control/Catalogo/listarCatalogoCombo',
                                        id: 'id_catalogo',
                                        root: 'datos',
                                        fields: ['id_catalogo','codigo','descripcion'],
                                        totalProperty: 'total',
                                        sortInfo: {
                                            field: 'codigo',
                                            direction: 'ASC'
                                        },
                                        baseParams:{
                                            start: 0,
                                            limit: 10,
                                            sort: 'descripcion',
                                            dir: 'ASC',
                                            par_filtro:'cat.descripcion',
                                            cod_subsistema:'KAF',
                                            catalogo_tipo:'tactivo_fijo__id_cat_estado_fun'
                                        }
                                    }),
                                    valueField: 'id_catalogo',
                                    hiddenValue: 'id_catalogo',
                                    displayField: 'descripcion',
                                    gdisplayField: 'estado_fun',
                                    mode: 'remote',
                                    triggerAction: 'all',
                                    lazyRender: true,
                                    pageSize: 15,
                                    tpl : '<tpl for="."><div class="x-combo-list-item"><p>{codigo} - {descripcion}</p></div></tpl>',
                                },{
                                    xtype: 'textarea',
                                    fieldLabel: 'Observaciones',
                                    name: 'observaciones',
                                    id: this.idContenedor+'_observaciones'
                                }]
                            }, {
                                title: 'Ubicación Física',
                                layout: 'form',
                                defaults: {
                                    width: 400
                                },
                                autoScroll: true,
                                defaultType: 'textfield',
                                items: [
                                  {
                                      xtype: 'combo',
                                      fieldLabel: 'Depósito',
                                      name: 'id_deposito',
                                      allowBlank: true,
                                      id: this.idContenedor+'_id_deposito',
                                      emptyText: 'Elija el depósito',
                                      store: new Ext.data.JsonStore({
                                          url: '../../sis_kactivos_fijos/control/Deposito/listarDeposito',
                                          id: 'id_deposito',
                                          root: 'datos',
                                          fields: ['id_deposito','id_funcionario','id_oficina','ubicacion','codigo','nombre','depto','depto_cod','funcionario','oficina_cod','oficina'],
                                          totalProperty: 'total',
                                          sortInfo: {
                                              field: 'codigo',
                                              direction: 'ASC'
                                          },
                                          baseParams:{
                                              start: 0,
                                              limit: 10,
                                              sort: 'codigo',
                                              dir: 'ASC',
                                              par_filtro:'depaf.codigo#depaf.nombre'
                                          }
                                      }),
                                      valueField: 'id_deposito',
                                      displayField: 'nombre',
                                      gdisplayField: 'deposito',
                                      mode: 'remote',
                                      triggerAction: 'all',
                                      lazyRender: true,
                                      pageSize: 15
                                  },
                                  {
                                      xtype: 'combo',
                                      fieldLabel: 'Oficina',
                                      name: 'id_oficina',
                                      allowBlank: true,
                                      id: this.idContenedor+'_id_oficina',
                                      emptyText: 'Elija la Oficina...',
                                      store: new Ext.data.JsonStore({
                                          url: '../../sis_organigrama/control/Oficina/listarOficina',
                                          id: 'id_oficina',
                                          root: 'datos',
                                          fields: ['id_oficina','codigo','nombre'],
                                          totalProperty: 'total',
                                          sortInfo: {
                                              field: 'nombre',
                                              direction: 'ASC'
                                          },
                                          baseParams:{
                                              start: 0,
                                              limit: 10,
                                              sort: 'codigo',
                                              dir: 'ASC',
                                              par_filtro:'ofi.nombre'
                                          }
                                      }),
                                      valueField: 'id_oficina',
                                      displayField: 'nombre',
                                      gdisplayField: 'oficina',
                                      mode: 'remote',
                                      triggerAction: 'all',
                                      lazyRender: true,
                                      pageSize: 15
                                  },
                                {
                                    xtype: 'textarea',
                                    fieldLabel: 'Ubicación',
                                    name: 'ubicacion',
                                    id: this.idContenedor+'_ubicacion',
                                    disabled: false
                                },{
                                    xtype: 'combo',
                                    fieldLabel: 'Lugar',
                                    name: 'id_lugar',
                                    allowBlank: true,
                                    id: this.idContenedor+'_id_lugar',
                                    emptyText: 'Elija el Lugar...',
                                    store: new Ext.data.JsonStore({
                                        url: '../../sis_parametros/control/Lugar/listarLugar',
                                        id: 'id_lugar',
                                        root: 'datos',
                                        fields: ['id_lugar','id_lugar_fk','codigo','nombre','tipo','sw_municipio','sw_impuesto','codigo_largo'],
                                        totalProperty: 'total',
                                        sortInfo: {
                                            field: 'nombre',
                                            direction: 'ASC'
                                        },
                                        baseParams:{
                                            start: 0,
                                            limit: 10,
                                            sort: 'nombre',
                                            dir: 'ASC',
                                            par_filtro:'lug.nombre'
                                        }
                                    }),
                                    valueField: 'id_lugar',
                                    displayField: 'nombre',
                                    gdisplayField: 'nombre_lugar',
                                    mode: 'remote',
                                    triggerAction: 'all',
                                    lazyRender: true,
                                    pageSize: 15
                                }
                              /*  {
                                    xtype: 'textarea',
                                    fieldLabel: 'Lugar',
                                    name: 'ubicacion',
                                    id: this.idContenedor+'_nombre_lugar',
                                    disabled: false
                                }*/]
                            }, {
                                title: 'Datos Compra',
                                layout: 'form',
                                defaults: {
                                    width: 400
                                },
                                defaultType: 'textfield',
                                items: [ {
                                    xtype: 'combo',
                                    fieldLabel: 'Proveedor',
                                    name: 'id_proveedor',
                                    allowBlank: true,
                                    id: this.idContenedor+'_id_proveedor',
                                    emptyText: 'Elija el Proveedor',
                                    store: new Ext.data.JsonStore({
                                        url: '../../sis_parametros/control/Proveedor/listarProveedorCombos',
                                        id: 'id_proveedor',
                                        root: 'datos',
                                        fields: ['id_proveedor','desc_proveedor'],
                                        totalProperty: 'total',
                                        sortInfo: {
                                            field: 'desc_proveedor',
                                            direction: 'ASC'
                                        },
                                        baseParams:{
                                            start: 0,
                                            limit: 10,
                                            sort: 'desc_proveedor',
                                            dir: 'ASC',
                                            par_filtro:'provee.desc_proveedor'
                                        }
                                    }),
                                    valueField: 'id_proveedor',
                                    displayField: 'desc_proveedor',
                                    gdisplayField: 'desc_proveedor',
                                    mode: 'remote',
                                    triggerAction: 'all',
                                    lazyRender: true,
                                    pageSize: 15,
                                    //valueNotFoundText: 'Proveedor no encontrado',
                                    pageSize: 15
                                },{
                                    xtype: 'datefield',
                                    fieldLabel: 'Fecha Compra',
                                    name: 'fecha_compra',
                                    allowBlank: false,
                                    id: this.idContenedor+'_fecha_compra'
                                }, {
                                    fieldLabel: 'Documento',
                                    name: 'documento',
                                    allowBlank: true,
                                    id: this.idContenedor+'_documento'
                                },{
                                    xtype: 'compositefield',
                                    fieldLabel: 'Importe',
                                   // msgTarget: 'side',
                                    anchor: '-20',
                                   /* defaults: {
                                        flex: 1
                                    },*/
                                    items: [{
                                        xtype: 'label',
                                        text: 'Costo AF'
                                    }, {
                                        xtype: 'numberfield',
                                        fieldLabel: 'Monto compra 87',
                                        name: 'precio_compra_87',
                                        allowBlank: true,
                                        id: this.idContenedor+'_precio_compra_87',
                                        width: 127
                                    }, {
                                        xtype: 'label',
                                        text: 'Valor Compra'
                                    }, {
                                        xtype: 'numberfield',
                                        fieldLabel: 'Monto compra 100',
                                        name: 'precio_compra',
                                        allowBlank: true,
                                        id: this.idContenedor+'_precio_compra',
                                        width: 127
                                    }]
                                }, {
                                    xtype: 'combo',
                                    fieldLabel: 'Estado Activo Compra',
                                    name: 'id_cat_estado_compra',
                                    allowBlank: false,
                                    id: this.idContenedor+'_id_cat_estado_compra',
                                    emptyText: 'Elija una opción',
                                    store: new Ext.data.JsonStore({
                                        url: '../../sis_parametros/control/Catalogo/listarCatalogoCombo',
                                        id: 'id_catalogo',
                                        root: 'datos',
                                        fields: ['id_catalogo','codigo','descripcion'],
                                        totalProperty: 'total',
                                        sortInfo: {
                                            field: 'descripcion',
                                            direction: 'ASC'
                                        },
                                        baseParams:{
                                            start: 0,
                                            limit: 10,
                                            sort: 'descripcion',
                                            dir: 'ASC',
                                            par_filtro:'cat.descripcion',
                                            cod_subsistema:'KAF',
                                            catalogo_tipo:'tactivo_fijo__id_cat_estado_compra'
                                        }
                                    }),
                                    valueField: 'id_catalogo',
                                    displayField: 'descripcion',
                                    gdisplayField: 'estado_compra',
                                    mode: 'remote',
                                    triggerAction: 'all',
                                    lazyRender: true,
                                    pageSize: 15
                                },{
                                    xtype: 'textfield',
                                    fieldLabel: 'Nro.Cbte Asociado',
                                    name: 'c31',
                                    allowBlank: true,
                                    id: this.idContenedor+'_c31',
                                    width: 140
                                }, {
                                    xtype: 'datefield',
                                    fieldLabel: 'Fecha.Cbte Asociado',
                                    name: 'fecha_cbte_asociado',
                                    allowBlank: true,
                                    id: this.idContenedor+'_fecha_cbte_asociado',
                                    width: 140
                                },{
                                    xtype: 'textfield',
                                    fieldLabel: 'Nro. de Tramite de Compra',
                                    name: 'tramite_compra',
                                    allowBlank: true,
                                    id: this.idContenedor+'_tramite_compra',
                                    width: 140
                                },{
                                    xtype: 'combo',
                                    fieldLabel: 'Subtipo',
                                    name: 'subtipo',
                                    allowBlank: true,
                                    mode: 'local',
                                    triggerAction: 'all',
                                    emptyText: 'Elija una opción',
                                    id: this.idContenedor+'_subtipo',
                                    emptyText: 'Elija una opción',
                                    store: new Ext.data.ArrayStore({
                                        id: 0,
                                        fields: ['subtipo'],
                                        data: [ ['Ninguno'], ['Leasing']]
                                    }),
                                    valueField: 'subtipo',
                                    displayField: 'subtipo'

                                },{
                                    xtype: 'combo',
                                    fieldLabel: 'Tipo de Movimiento',
                                    name: 'movimiento',
                                    allowBlank: true,
                                    mode: 'local',
                                    triggerAction: 'all',
                                    emptyText: 'Elija una opción',
                                    id: this.idContenedor+'_movimiento',
                                    emptyText: 'Elija una opción',
                                    store: new Ext.data.ArrayStore({
                                        id: 0,
                                        fields: ['movimiento'],
                                        data: [ ['Transito'], ['Normal']]
                                    }),
                                    valueField: 'movimiento',
                                    displayField: 'movimiento'

                                }]
                            }, {
                                title: 'Datos Depreciación',
                                layout: 'form',
                                defaults: {
                                    width: 400
                                },
                                defaultType: 'textfield',
                                items: [{
                                    xtype: 'datefield',
                                    fieldLabel: 'Fecha inicio Dep/Act',
                                    //qtip:'Fecha de inicio de depreciación o de actualización',
                                    name: 'fecha_conformidad',
                                    allowBlank: true,
                                    id: this.idContenedor+'_fecha_conformidad'
                                },
                                {
                                    fieldLabel: 'Monto Vigente',
                                    name: 'monto_compra',
                                    disabled: false,
                                    allowBlank: true,
                                    id: this.idContenedor+'_monto_compra'
                                },{
                                            xtype: 'combo',
                                            name:'id_proyecto',
                                            id: this.idContenedor+'_id_proyecto',
                                            qtip: 'Proyecto o aplicación del activo fijo, se utliza para cargar los gastos  de depreciación (Determinar los centro de costos)',
                                            fieldLabel:'Proyecto / Aplicación',
                                            allowBlank:false,
                                            emptyText:'Proyecto...',
                                            store: new Ext.data.JsonStore({
                                                url: '../../sis_parametros/control/Proyecto/ListarProyecto',
                                                id: 'id_proyecto',
                                                root: 'datos',
                                                sortInfo:{
                                                    field: 'codigo_proyecto',
                                                    direction: 'ASC'
                                                },
                                                totalProperty: 'total',
                                                fields: ['id_proyecto','codigo_proyecto','nombre_proyecto'],
                                                // turn on remote sorting
                                                remoteSort: true,
                                                baseParams:{par_filtro:'codigo_proyecto#nombre_proyecto'}
                                            }),
                                            valueField: 'id_proyecto',
                                            displayField: 'codigo_proyecto',
                                            gdisplayField:'desc_proyecto',//mapea al store del grid
                                            tpl:'<tpl for="."><div class="x-combo-list-item"><p>{codigo_proyecto}</p><p>{nombre_proyecto}</p> </div></tpl>',
                                            hiddenName: 'id_proyecto',
                                            forceSelection:true,
                                            typeAhead: true,
                                            triggerAction: 'all',
                                            lazyRender:true,
                                            mode:'remote',
                                            pageSize:10,
                                            queryDelay:1000,
                                            minChars:2
                                    }
                                ]
                            }]
                        }]
                    }],
                    //fileUpload: me.fileUpload,
                    padding: this.paddingForm,
                    bodyStyle: this.bodyStyleForm,
                    border: this.borderForm,
                    frame: this.frameForm,
                    autoScroll: false,
                    autoDestroy: true,
                    autoScroll: false,
                    region: 'center'
                });

                this.afWindow = new Ext.Window({
                    width: 590,
                    height: 700,
                    modal: true,
                    closeAction: 'hide',
                    labelAlign: 'top',
                    title: 'Registro Preingreso',
                    bodyStyle: 'padding:5px',
                    layout: 'border',
                    items: [{
                        region: 'west',
                        split: false,
                        //width: 200,
                      //  minWidth: 150,
                        //maxWidth: 250
                        /*items: [{
                            id: 'img-detail-panel',
                            region: 'north'
                        }, {
                            id: 'img-qr-panel'+this.idContenedor,
                            region: 'center'
                        }]*/
                    },this.form],
                    buttons: [{
                        text: 'Guardar',
                        handler: this.onSubmit,
                        scope: this
                    }, {
                        text: 'Declinar',
                        handler: function() {
                            this.afWindow.hide();
                        },
                        scope: this
                    }]
                });



                /*Ext.getCmp(this.idContenedor+'_id_proveedor').on('blur',function(cmp,rec,index){
                  Ext.getCmp(this.idContenedor+'_id_proveedor').setValue(rec.data.id_proveedor);
                  console.log('RECUPERANDO DATO PROVEEDOR',rec.data.id_proveedor);
                },this);*/

              /*  console.log('RECUPERANDO DATO PROVEEDOR',this.maestro.desc_proveedor);
                this.getComponente(this.idContenedor+'_id_proveedor').setValue(this.maestro.desc_proveedor);
                console.log('RECUPERANDO DATO PROVEEDOR',this.idContenedor+'_id_proveedor');*/


                Ext.getCmp(this.idContenedor+'_id_clasificacion').on('select',function(cmp,rec,index){
                    if(rec.data.depreciable == 'si'){
                        Ext.getCmp(this.idContenedor+'_vida_util_original').setValue(rec.data.vida_util);
                        //Convierte a años
                        Ext.getCmp(this.idContenedor+'_vida_util_original_anios').setValue(this.convertirVidaUtil(rec.data.vida_util));
                    } else {
                        Ext.getCmp(this.idContenedor+'_vida_util_original').allowBlank = true;
                        Ext.getCmp(this.idContenedor+'_vida_util_original_anios').allowBlank = true;
                        Ext.getCmp(this.idContenedor+'_vida_util_original').setValue('')
                        Ext.getCmp(this.idContenedor+'_vida_util_original_anios').setValue('')
                    }
                  //  this.actualizarSegunClasificacion(rec.data.tipo_activo, rec.data.depreciable);

                },this);
                //Vida util
                Ext.getCmp(this.idContenedor+'_vida_util_original').on('blur',function(cmp,rec,index){

                    //Convierte a años
                    Ext.getCmp(this.idContenedor+'_vida_util_original_anios').setValue(this.convertirVidaUtil(Ext.getCmp(this.idContenedor+'_vida_util_original').getValue()));

                },this);
                //Vida util años
                Ext.getCmp(this.idContenedor+'_vida_util_original_anios').on('blur',function(cmp,rec,index){
                    //Convertir a meses
                    Ext.getCmp(this.idContenedor+'_vida_util_original').setValue(this.convertirVidaUtil(Ext.getCmp(this.idContenedor+'_vida_util_original_anios').getValue(),'anios'));
                },this);



        },

        convertirVidaUtil(cantidad,tipo='mes'){
            var valor=0;
            if(tipo=='anios'){
                //Convierte de años a meses
                valor = Ext.util.Format.round(cantidad * 12,0);
            } else {
                //Convierte de meses a años
                valor = Ext.util.Format.round(cantidad / 12,2);
            }
            return valor;
        },

        abrirVentana: function(tipo){
            var data;
            if(tipo=='edit'){
                //Carga datos
                this.cargaFormulario(this.sm.getSelected().data);
                data = this.sm.getSelected().data;
            }
            //Renderea la imagen, abre la ventana
            this.afWindow.show();
            this.renderFoto(data);
        },

        onSubmit: function(o,x,force){
            var formData;
            if(this.form.getForm().isValid()){
                Phx.CP.loadingShow();
                formData = this.dataSubmit();
                console.log('EL DATOS ES:',formData);
                Ext.Ajax.request({
                    url: '../../sis_almacenes/control/PreingresoDet/insertarPreingresoDetPreparacion',
                    params: this.dataSubmit,
                    isUpload: false,
                    success: function(a,b,c){
                        this.store.rejectChanges();
                        Phx.CP.loadingHide();
                        this.afWindow.hide();
                        this.reload();
                    },
                    argument: this.argumentSave,
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });
            }else {
                Ext.MessageBox.alert('Validación','Existen datos Incompletos en el Formulário verifique porfavor.');
            }
        },
        dataSubmit: function(){
            var submit={};
            console.log('EL DATOS SUBMIT ES:',submit);
            Ext.each(this.form.getForm().items.keys, function(element, index){
                obj = Ext.getCmp(element);
                if(obj.items){
                    Ext.each(obj.items.items, function(elm, ind){
                        submit[elm.name]=elm.getValue();
                    },this)
                } else {
                    submit[obj.name]=obj.getValue();
                    if(obj.name=='id_clasificacion'){
                        if(obj.selectedIndex!=-1){
                            submit[obj.name]=obj.store.getAt(obj.selectedIndex).id;
                        }
                    }
                }
            },this);
            return submit;
        },

        filtrarGrid: function(data){
            Ext.apply(this.grid.store.baseParams,data);
            this.load();
        },

        cargaFormulario: function(data){
            var obj,key,objsec,keysec;
            //console.log('FORMULARIO:',obj);
            Ext.each(this.form.getForm().items.keys, function(element, index){
                obj = Ext.getCmp(element);
                if(obj.items){
                    Ext.each(obj.items.items, function(elm, b, c){
                        if(elm.getXType()=='combo'&&elm.mode=='remote'&&elm.store!=undefined){
                            if (!elm.store.getById(data[elm.name])) {
                                rec = new Ext.data.Record({[elm.displayField]: data[elm.gdisplayField], [elm.valueField]: data[elm.name] },data[elm.name]);
                                elm.store.add(rec);
                                elm.store.commitChanges();
                                elm.modificado = true;
                            }
                        }
                        elm.setValue(data[elm.name]);
                    },this);
                } else {
                    key = element.replace(this.idContenedor+'_','');
                    if(obj.getXType()=='combo'&&obj.mode=='remote'&&obj.store!=undefined){
                        if (!obj.store.getById(data[key])) {
                            rec = new Ext.data.Record({[obj.displayField]: data[obj.gdisplayField], [obj.valueField]: data[key] },data[key]);
                            obj.store.add(rec);
                            obj.store.commitChanges();
                            obj.modificado = true;
                            //console.log('key:'+key,',gdisplayField:'+obj.gdisplayField,',data[obj.gdisplayField]:'+data[obj.gdisplayField],',obj.valueField:'+obj.valueField,',data[key]:'+data[key]);
                            //console.log(rec,obj.store, data[key],obj.valueField);
                        }

                    }
                    obj.setValue(data[key]);
                }
            },this);

        },
        renderFoto: function(data){

            this.detailsTemplate.overwrite(data);


           /*if(data.codigo==''){
                qrcode.clear();
            } else {
                qrcode.makeCode(data.codigo);
            }*/
        },


        preparaMenu:function(n){
            Phx.vista.PreingresoDetModActV2.superclass.preparaMenu.call(this,n);
            this.preparaComponentes(this.maestro)
        },

        loadValoresIniciales:function(){
            Phx.vista.PreingresoDetModActV2.superclass.loadValoresIniciales.call(this);
            this.getComponente('id_preingreso').setValue(this.maestro.id_preingreso);
            this.Cmp.fecha_conformidad.setValue(this.maestro.fecha_conformidad);
            this.Cmp.fecha_compra.setValue(this.maestro.fecha_conformidad);
            this.Cmp.c31.setValue(this.Cmp.c31.getValue());

        },

        onReloadPage:function(m){
            this.maestro=m;

            Ext.apply(this.store.baseParams,{id_preingreso:this.maestro.id_preingreso,estado: this.estado});
            //this.preparaComponentes(this.maestro);
            this.load({params:{start:0, limit:this.tam_pag}});
        },
        onButtonEdit: function (){
          console.log('RECUPERANDO DATO PROVEEDOR',this.maestro.desc_proveedor);
          //Ext.getCmp(this.idContenedor+'_id_proveedor').setValue(this.maestro.desc_proveedor);
          this.crearVentana();
          this.abrirVentana('edit');

        //  this.getComponente('id_proveedor').setValue(this.maestro.desc_proveedor);


            //Prepara los componentes en función de si el preingreso es para Almacén o para Activos Fijos
            Phx.vista.PreingresoDetModActV2.superclass.onButtonEdit.call(this);
            if (this.Cmp.fecha_conformidad.getValue() == '' || this.Cmp.fecha_conformidad.getValue() == undefined) {
                this.Cmp.fecha_conformidad.setValue(this.maestro.fecha_conformidad);
                this.Cmp.fecha_compra.setValue(this.maestro.fecha_conformidad);
            }

            if (this.Cmp.c31.getValue() == '' || this.Cmp.c31.getValue() == undefined) {
                this.Cmp.c31.setValue(this.Cmp.c31.getValue());
            }


            this.preparaComponentes(this.maestro)

        },
        preparaComponentes: function(pMaestro){
            var codSis;

            if(pMaestro.tipo=='activo_fijo'){
                //Setea store del departamento
                codSis='KAF';
                Ext.apply(this.Cmp.id_depto.store.baseParams,{codigo_subsistema:codSis});

                //Habilita componentes
                this.Cmp.precio_compra_87.enable();
                this.mostrarComponente(this.Cmp.precio_compra_87);
                this.Cmp.id_clasificacion.enable();
                this.mostrarComponente(this.Cmp.id_clasificacion);
                this.Cmp.id_depto.enable();
                this.mostrarComponente(this.Cmp.id_depto);
                this.Cmp.nombre.enable();
                this.mostrarComponente(this.Cmp.nombre);
                this.Cmp.descripcion.enable();
                this.mostrarComponente(this.Cmp.descripcion);
                this.Cmp.id_lugar.enable();
                this.mostrarComponente(this.Cmp.id_lugar);
                this.Cmp.ubicacion.enable();
                this.mostrarComponente(this.Cmp.ubicacion);
                this.Cmp.c31.enable();
                this.mostrarComponente(this.Cmp.c31);
                this.Cmp.fecha_conformidad.enable();
                this.mostrarComponente(this.Cmp.fecha_conformidad);
                this.mostrarComponente(this.Cmp.fecha_compra);
                this.mostrarColumna(5);
                this.mostrarColumna(8);
                this.mostrarColumna(9);
                this.mostrarColumna(10);
                this.mostrarColumna(11);
                this.mostrarColumna(12);
                this.mostrarColumna(13);
                this.mostrarColumna(14);
                this.mostrarColumna(15);
                this.mostrarColumna(16);

                //Deshabilita componentes
                this.Cmp.id_almacen.disable();
                this.ocultarComponente(this.Cmp.id_almacen);
                this.Cmp.id_item.disable();
                this.ocultarComponente(this.Cmp.id_item);
                this.ocultarColumna(5);
                this.ocultarColumna(6);

            } else if(pMaestro.tipo=='almacen'){
                //Setea store del departamento
                codSis='ALM';
                Ext.apply(this.Cmp.id_depto.store.baseParams,{codigo_subsistema:codSis});

                //Habilita componentes
                this.Cmp.id_almacen.enable();
                this.mostrarComponente(this.Cmp.id_almacen);
                this.Cmp.id_item.enable();
                this.mostrarComponente(this.Cmp.id_item);
                this.mostrarColumna(5);
                this.mostrarColumna(6);

                //Deshabilita componentes
                this.Cmp.precio_compra_87.disable();
                this.ocultarComponente(this.Cmp.precio_compra_87);
                this.Cmp.id_clasificacion.disable();
                this.ocultarComponente(this.Cmp.id_clasificacion);
                this.Cmp.id_depto.disable();
                this.ocultarComponente(this.Cmp.id_depto);
                this.Cmp.nombre.disable();
                this.ocultarComponente(this.Cmp.nombre);
                this.Cmp.descripcion.disable();
                this.ocultarComponente(this.Cmp.descripcion);
                this.Cmp.id_lugar.disable();
                this.ocultarComponente(this.Cmp.id_lugar);
                this.Cmp.ubicacion.disable();
                this.ocultarComponente(this.Cmp.ubicacion);
                this.Cmp.c31.disable();
                this.ocultarComponente(this.Cmp.c31);
                this.ocultarComponente(this.Cmp.fecha_conformidad);
                this.ocultarComponente(this.Cmp.fecha_compra);
                this.ocultarColumna(5);
                this.ocultarColumna(8);
                this.ocultarColumna(9);
                this.ocultarColumna(10);
                this.ocultarColumna(11);
                this.ocultarColumna(12);
                this.ocultarColumna(13);
                this.ocultarColumna(14);
                this.ocultarColumna(15);
                this.ocultarColumna(16);

            } else {
                //Setea store del departamento
                codSis='error';
                Ext.apply(this.Cmp.id_depto.store.baseParams,{codigo_subsistema:codSis});
                this.ocultarColumna(5);
                this.ocultarColumna(6);
                this.ocultarColumna(7);
                this.ocultarColumna(8);
            }

            console.log(pMaestro.estado);

            //Habilita los componentes
            if(pMaestro.estado=='borrador'){
                this.getBoton('new').enable();
                this.getBoton('edit').enable();
                this.getBoton('btnAgTodos').enable();
            } else{
                this.getBoton('new').disable();
                this.getBoton('edit').disable();
                this.getBoton('btnAgTodos').disable();
            }
        },

        aplicarFiltro: function(){
            this.store.baseParams.estado=this.estado;
            this.load();
        },

        quitarTodos: function(){
            //Verifica si el grid tiene registros cargados
            if(this.store.getTotalCount()>0){
                Ext.Msg.show({
                    title:'Confirmación',
                    msg: '¿Está seguro de quitar todos los items del Preingreso?',
                    buttons: Ext.Msg.YESNO,
                    fn: function(a,b,c){
                        if(a=='yes'){
                            var myPanel = Phx.CP.getPagina(this.idContenedorPadre);
                            Phx.CP.loadingShow();
                            Ext.Ajax.request({
                                url: '../../sis_almacenes/control/PreingresoDet/quitaPreingresoAll',
                                params: {
                                    id_preingreso: this.maestro.id_preingreso
                                },
                                success: function(a,b,c){
                                    Phx.CP.loadingHide();
                                    this.reload();
                                    //Carga datos del panel derecho
                                    myPanel.onReloadPage(this.maestro);
                                    delete myPanel;
                                },
                                failure: this.conexionFailure,
                                timeout: this.timeout,
                                scope: this
                            });
                        }
                    },
                    icon: Ext.MessageBox.QUESTION,
                    scope: this
                });

            }
        },

        successDel:function(resp){
            Phx.CP.loadingHide();
            this.reload();

            //Recarga al padre
            var myPanel = Phx.CP.getPagina(this.idContenedorPadre);
            myPanel.onReloadPage(this.maestro);
            delete myPanel;
        },

        oncellclick : function(grid, rowIndex, columnIndex, e) {

            var record = this.store.getAt(rowIndex),
                fieldName = grid.getColumnModel().getDataIndex(columnIndex); // Get field name

            if (fieldName == 'quitar') {

                var myPanel = Phx.CP.getPagina(this.idContenedorPadre);

                if(this.maestro.estado == 'finalizado'){
                    Ext.Msg.alert('Acción no permitida','El preingreso ya fue finalizado, no puede hacerse ninguna modificación.');
                } else {
                    Phx.CP.loadingShow();
                    Ext.Ajax.request({
                        url : '../../sis_almacenes/control/PreingresoDet/eliminarPreingresoDetPreparacion',
                        params : {
                            id_preingreso_det:	record.data.id_preingreso_det,
                            data: record
                        },
                        success : function(a,b,c){
                            Phx.CP.loadingHide();
                            this.reload();
                            //Carga datos del panel derecho
                            myPanel.onReloadPage(this.maestro);
                            delete myPanel;
                        },
                        failure : this.conexionFailure,
                        timeout : this.timeout,
                        scope : this
                    });
                }

            }

        }

    })
</script>
