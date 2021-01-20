<?php
/**
 *@package pXP
 *@file    GenerarReporteExistencias.php
 *@author  Ariel Ayaviri Omonte
 *@date    02-05-2013
 *@description Archivo con la interfaz para generación de reporte
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
	Phx.vista.GenerarReporteExistencias = Ext.extend(Phx.frmInterfaz, {
		Atributos : [
            {
                config : {
                    name : 'formato',
                    fieldLabel : 'Formato',
                    allowBlank : false,
                    triggerAction : 'all',
                    lazyRender : true,
                    mode : 'local',
                    store : new Ext.data.ArrayStore({
                        fields : ['tipo', 'valor'],
                        data : [
                            ['antiguo', 'Existencias Prec. Unit. Promedio'],
                            ['ingresos', 'Existencias Prec. Unit. Desglosado (Nuevo)'],
                            ['nuevo', 'Existencias Detalle Ingreso/Salida (Nuevo)'],
                            ['ministerio', 'Existencias Ministerio (Nuevo)']
                        ]
                    }),
                    anchor : '100%',
                    valueField : 'tipo',
                    displayField : 'valor',
                    //listWidth:183,
                    resizable: true
                },
                type : 'ComboBox',
                id_grupo : 0,
                form : true
            },
		    {
			config : {
				name : 'id_almacen',
				fieldLabel : 'Almacen',
				allowBlank : false,
				emptyText : 'Almacen...',
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
				gdisplayField : 'nombre_almacen',
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
					return String.format('{0}', record.data['nombre_almacen']);
				}
			},
			type : 'ComboBox',
			id_grupo : 0,
			grid : true,
			form : true
		},
        /*{
            config : {
                name : 'fecha_ini',
                //id:'fecha_ini'+this.idContenedor,
                fieldLabel : 'Fecha Desde',
                allowBlank : false,
                gwidth : 100,
                format : 'd/m/Y',
                renderer : function(value, p, record) {
                    return value ? value.dateFormat('d/m/Y h:i:s') : ''
                }/!*,
                vtype: 'daterange',
                endDateField: 'fecha_fin'+this.idContenedor*!/
            },
            type : 'DateField',
            id_grupo : 0,
            grid : true,
            form : true
        },*/
        {
			config : {
				name : 'fecha_hasta',
				fieldLabel : 'Fecha Hasta',
				allowBlank : false,
				gwidth : 100,
				format : 'd/m/Y',
				renderer : function(value, p, record) {
					return value ? value.dateFormat('d/m/Y h:i:s') : ''
				}
			},
			type : 'DateField',
			id_grupo : 0,
			grid : true,
			form : true
		}, {
			config: {
				name: 'all_items',
				fieldLabel: 'Seleccionar Criterio',
				anchor: '100%',
				allowBlank: false,
				origen: 'CATALOGO',
				gdisplayField: 'all_items',
				gwidth: 100,
				tinit:false,
				baseParams:{
						cod_subsistema:'ALM',
						catalogo_tipo:'titem__opciones'
				},
				renderer:function (value, p, record){return String.format('{0}', record.data['all_items']);}
			},
			type: 'ComboRec',
			id_grupo: 0,
			grid: true,
			form: true
		}, 
		
		
		{
			config : {
				name : 'id_items',
				fieldLabel : 'Item',
				allowBlank : true,
				emptyText : 'Items...',
				store : new Ext.data.JsonStore({
					url : '../../sis_almacenes/control/Item/listarItemNotBase',
					id : 'id_item',
					root : 'datos',
					sortInfo : {
						field : 'nombre',
						direction : 'ASC'
					},
					totalProperty : 'total',
					fields : ['id_item', 'nombre', 'codigo', 'desc_clasificacion', 'codigo_unidad', 'nombre_completo'],
					remoteSort : true,
					baseParams : {
						par_filtro : 'item.nombre#item.codigo#cla.nombre'
					}
				}),
				valueField : 'id_item',
				//hiddenValue: 'id_item',
				displayField : 'nombre_completo',
				gdisplayField : 'nombre_item',
				//tpl : '<tpl for="."><div class="x-combo-list-item"><p>Nombre: {nombre}</p><p>Código: {codigo}</p><p>Clasif.: {desc_clasificacion}</p></div></tpl>',
                tpl: new Ext.XTemplate([
                    '<tpl for=".">',
                    '<div class="x-combo-list-item">',
                    '<div class="awesomecombo-item {checked}">',
                    '<p><b>Nombre: {nombre}</b></p>',
                    '</div><p><b>Código: </b> <span style="color: green;">{codigo}</span></p>' +
                    '<p><b>Clasif.:</b> <span style="color: green;">{desc_clasificacion}</span></p>',
                    '</div></tpl>'
                ]),
				hiddenName : 'id_items',
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
					return String.format('{0}', record.data['nombre_item']);
				},
				enableMultiSelect : false
			},
			type : 'AwesomeCombo',
			id_grupo : 0,
			grid : false,
			form : true
		},
		/*{
			config: {
				name: 'btn_clasif__',
				fieldLabel: 'Clasificacion__',
				anchor: '100%'
			},
			type: 'Button',
			id_grupo: 0,
			grid: true,
			form: false
		},*/ 
		/*{
			config : {
				name : 'clasificacion',
				fieldLabel : 'Clasificación',
				allowBlank : true,
				items: [
					{
                        xtype     : 'textarea',
                        name      : 'clasif_desc',
                        fieldLabel: 'Start', 
                        width:'80%',
                        disabled:true
                   },{
                        xtype     : 'button',
                        name      : 'btn_clasif',
                        fieldLabel: 'B',
                        listeners: {
					    	'click': {
					    		fn: function(a,b) {
					    				var data;
					    				console.log(this)
					    				console.log(this.idContenedor)
					    				//alert('hhhhhhh')
					     				Phx.CP.loadWindows('../../../sis_almacenes/vista/clasificacion/BuscarClasificacion.php',
												'Clasificación',
												{
													width:'60%',
													height:'70%',
													evento: 'beforeclose',
													manejadorEvento: function(){
														alert('on close')
													}
											    },
											    data,
											    this.idContenedor,
											    'BuscarClasificacion'
										);
					    	},
					    	scope:this
					    }
					    	
					  }
                    }]
			},
			type : 'CompositeField',
			id_grupo : 0,
			grid : true,
			form : true
		},*/
		{
			config: {
				name: 'clasificacion',
				fieldLabel: 'Clasificación',
				anchor: '100%'
			},
			type: 'TextArea',
			id_grupo: 0,
			grid: true,
			form: true
		},
		{
			config : {
				name : 'alertas',
				fieldLabel : 'Generar reporte de alertas',
				allowBlank : false,
				triggerAction : 'all',
				lazyRender : true,
				mode : 'local',
				store : new Ext.data.ArrayStore({
					fields : ['codigo', 'nombre'],
					data : [['no', 'No'],['cantidad_minima', 'Cantidad mínima']/*, ['cantidad_amarilla', 'Cantidad alerta amarilla'], ['cantidad_roja', 'Cantidad alerta roja']*/]
				}),
				anchor : '50%',
				valueField : 'codigo',
				displayField : 'nombre'
			},
			type : 'ComboBox',
			id_grupo : 0,
			form : true
		},
		{
			config : {
				name : 'saldo_cero',
				fieldLabel : 'Items Sin Existencias',
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
			id_grupo : 0,
			form : true
		},
			{
				config : {
					name : 'porcentaje',
					fieldLabel : 'Porcentaje Existencia',
					allowBlank : false,
					triggerAction : 'all',
					lazyRender : true,
					mode : 'local',
					store : new Ext.data.ArrayStore({
						fields : ['codigo', 'valor'],
						data : [['ochenta', '87 %'], ['cien', '100 %']]
					}),
					anchor : '50%',
					valueField : 'codigo',
					displayField : 'valor'
				},
				type : 'ComboBox',
				id_grupo : 0,
				form : true
			},
		{
			config : {
				name : 'id_clasificacion',
				inputType:'hidden',
				labelSeparator:'',
			},
			type:'Field',
			form:true
		},
            {
                config:{
                    name:'formato_reporte',
                    fieldLabel:'Formato del Reporte',
                    typeAhead: true,
                    allowBlank:false,
                    triggerAction: 'all',
                    emptyText:'Formato...',
                    selectOnFocus:true,
                    mode:'local',
                    store:new Ext.data.ArrayStore({
                        fields: ['ID', 'valor'],
                        data :	[['pdf','PDF'],
                            ['xls','XLS']]
                    }),
                    valueField:'ID',
                    displayField:'valor',
                    width:250

                },
                type:'ComboBox',
                id_grupo:1,
                form:true
            }

        ],
		title : 'Generar Reporte Anual',
		ActSave : '../../sis_almacenes/control/Reportes/reporteExistencias',
		topBar : true,
		botones : false,
		labelSubmit : 'Imprimir',
		tooltipSubmit : '<b>Generar Reporte de Existencias</b>',
        timeout: 1000000,
		constructor : function(config) {
			Phx.vista.GenerarReporteExistencias.superclass.constructor.call(this, config);
			this.init();
			
			this.getComponente('catalogo').on('select', function(e, component, index) {
                

			    if (e.value == 'Todos los Items') {
                    this.getComponente('id_items').reset();
                    this.getComponente('id_items').disable();
                    this.getComponente('clasificacion').reset();
                    this.getComponente('clasificacion').disable();
                    this.getComponente('id_items').c = true;
                    this.getComponente('clasificacion').modificado = true;
                    this.getComponente('id_items').allowBlank=true;
                    this.getComponente('clasificacion').allowBlank=true;
                    
                } else if(e.value == 'Seleccionar Items') {
                    this.getComponente('id_items').enable();
                    this.getComponente('clasificacion').reset();
                    this.getComponente('clasificacion').disable();
                    this.getComponente('clasificacion').modificado = true;
                    this.getComponente('id_items').allowBlank=false;
                    this.getComponente('clasificacion').allowBlank=true;
                } else if(e.value == 'Por Clasificacion') {
                    this.getComponente('id_items').reset();
                    this.getComponente('id_items').modificado = true;
                	this.getComponente('id_items').disable();
                    this.getComponente('clasificacion').enable();
                    this.getComponente('id_items').allowBlank=true;
                    this.getComponente('clasificacion').allowBlank=false;
                	
                } else{
                	this.getComponente('id_items').disable();
                    this.getComponente('clasificacion').disable();
                    this.getComponente('id_items').allowBlank=true;
                    this.getComponente('clasificacion').allowBlank=true;
                }
			}, this);
			
			this.getComponente('id_items').disable();
			this.Cmp.id_almacen.on('select',this.onAlmacenSelect,this);
			this.Cmp.clasificacion.on('focus',this.bntClasificacion,this);
			this.Cmp.clasificacion.setReadOnly(true);
			this.clasificacion = this.Cmp.clasificacion;
			this.Cmp.alertas.setValue('no');    
		},
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
				columnWidth : '500px',
				items : [],
				id_grupo : 0,
				collapsible : true
			}]
		}],
		onAlmacenSelect : function () {
			this.Cmp.id_item.store.baseParams.id_almacen = this.maestro.id_almacen;
			this.Cmp.id_item.modificado = true;
		},
		bntClasificacion: function(){
			var data;
			//Valida que el combo de criterio sea por Clasificación
			if(this.Cmp.all_items.getValue()=='Por Clasificacion'){
				Phx.CP.loadWindows('../../../sis_almacenes/vista/clasificacion/BuscarClasificacion.php',
						'Clasificación',
						{
							width:'60%',
							height:'70%'
					    },
					    data,
					    this.idContenedor,
					    'BuscarClasificacion'
				);
			}
		},
		id_clasificacion:'',
		clasificacion:'',
		agregarArgsExtraSubmit: function(){
			//Inicializa el objeto de los argumentos extra
			this.argumentExtraSubmit={};
				//Añade los parámetros extra para mandar por submit
			this.argumentExtraSubmit.id_clasificacion=this.id_clasificacion;
			this.argumentExtraSubmit.almacen=this.Cmp.id_almacen.getRawValue();
		},
        successSave :function(resp){
            Phx.CP.loadingHide();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            if (reg.ROOT.error) {
                alert('error al procesar');
                return
            }

            var nomRep = reg.ROOT.detalle.archivo_generado;
            if(Phx.CP.config_ini.x==1){
                nomRep = Phx.CP.CRIPT.Encriptar(nomRep);
            }

            if(this.Cmp.formato_reporte.getValue()=='pdf'){
                window.open('../../../lib/lib_control/Intermediario.php?r='+nomRep+'&t='+new Date().toLocaleTimeString())
            }
            else{
                window.open('../../../reportes_generados/'+nomRep+'?t='+new Date().toLocaleTimeString())
            }

        }

})
</script>