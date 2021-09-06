<?php
/**
 *@package pXP
 *@file    Item.php
 *@author  Gonzalo Sarmiento
 *@date    21-09-2012
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
	Phx.vista.Item = Ext.extend(Phx.gridInterfaz, {
		constructor : function(config) {
			this.maestro = config.maestro;
			Phx.vista.Item.superclass.constructor.call(this, config);
			this.init();
			this.tbar.items.get('b-new-' + this.idContenedor).hide();
			this.grid.getTopToolbar().disable();
			this.grid.getBottomToolbar().disable();
			this.store.removeAll();
			this.addButton('btnGenerarCodigo', {
				text : 'Generar Código',
				iconCls : 'bok',
				disabled : true,
				handler : this.btnGenerarCodigoHandler,
				tooltip : '<b>Actividades</b><br/>Generar Código del item'
			});
			this.addButton('btnVerReemplazos', {
				text : 'Ver Reemplazos',
				iconCls : 'bengineadd',
				disabled : true,
				handler : this.onBtnVerReemplazos,
				tooltip : '<b>Actividades</b><br/>Ver los Items de Reemplazo del item seleccionado'
			});
			this.addButton('btnVerArchivos', {
				text : 'Ver Archivos',
				iconCls : 'bdocuments',
				disabled : true,
				handler : this.onBtnVerArchivos,
				tooltip : '<b>Actividades</b><br/>Ver los archivos asociados al item seleccionado'
			});
		},
		Atributos : [{
			config : {
				labelSeparator : '',
				inputType : 'hidden',
				name : 'id_item'
			},
			type : 'Field',
			form : true
		}, {
			config : {
				labelSerparator : '',
				inputType : 'hidden',
				name : 'id_clasificacion',
			},
			type : 'Field',
			form : true
		}, {
            config:{
                    name:'id_unidad_medida',
                    allowBlank:false,
                    origen:'UNIDADMEDIDA',
                    tinit:true,
                    fieldLabel:'Unidad Medida',
                    gdisplayField:'codigo_unidad',
                    anchor: '100%',
                    gwidth: 70,
                    tipo : "All",
                    renderer:function (value, p, record){return String.format('{0}', record.data['codigo_unidad']);}
                 },
                type:'ComboRec',
                id_grupo:0,
                filters:{   
                    pfiltro:'umed.codigo',
                    type:'string'
                },
                grid:true,
                form:true
        }, 
		
		{
            config : {
                name : 'nombre',
                fieldLabel : 'Nombre',
                allowBlank : false,                
                store : new Ext.data.JsonStore({
                    url : '../../sis_almacenes/control/Item/listarItem',
                    id : 'nombre',
                    root : 'datos',
                    sortInfo : {
                        field : 'nombre',
                        direction : 'ASC'
                    },
                    totalProperty : 'total',
                    fields : ['nombre', 'codigo','descripcion'],
                    remoteSort : true,
                    baseParams : {
                        par_filtro : 'item.nombre#item.descripcion',
                        start:0,
                        limit:1000
                    }
                }),
                valueField : 'nombre',
                displayField : 'nombre',
                gdisplayField : 'nombre',
                tpl : '<tpl for="."><div class="x-combo-list-item"><p>Código: {codigo}</p><p>Nombre: {nombre}</p><p>Descripcion: {descripcion}</p></div></tpl>',
                hiddenName : 'nombre',
                forceSelection : false,
                typeAhead : false,
                hideTrigger : true,
                lazyRender : true,
                mode : 'remote',
                pageSize : 0,
                queryDelay : 1000,
                anchor : '100%',
                gwidth : 150,
                minChars : 4
            },
            type : 'ComboBox',
            id_grupo : 0,
            filters : {
                pfiltro : 'item.nombre',
                type : 'string'
            },
            grid : true,
            form : true
        },{
			config : {
				name : 'codigo',
				fieldLabel : 'Código',
				allowBlank : false,
				width : '100%',
				gwidth : 150,
				maxLength : 20
			},
			type : 'TextField',
			filters : {
				pfiltro : 'item.codigo',
				type : 'string'
			},
			id_grupo : 1,
			grid : true,
			form : false
		}, {
			config : {
				name : 'descripcion',
				fieldLabel : 'Descripción',
				allowBlank : true,
				width : '100%',
				gwidth : 150,
				maxLength : 1000
			},
			type : 'TextArea',
			filters : {
				pfiltro : 'item.descripcion',
				type : 'string'
			},
			id_grupo : 1,
			grid : true,
			form : true
		},
		{
			config : {
				name : 'id_almacen',
				fieldLabel : 'Almacenes habilitados',
				allowBlank : true,
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
				gdisplayField : 'almacenes_habilitados',
				hiddenName : 'id_almacen',
				forceSelection : true,
				typeAhead : false,
				triggerAction : 'all',
				lazyRender : true,
				mode : 'remote',
				pageSize : 10,
				queryDelay : 1000,				
				gwidth : 150,
				minChars : 2,
				enableMultiSelect:true,
			},
			type : 'AwesomeCombo',
			id_grupo : 0,			
			grid : true,
			form : true
		}, {
			config : {
				name : 'palabras_clave',
				fieldLabel : 'Palabras clave',
				allowBlank : true,
				width : '100%',
				gwidth : 80,
				maxLength : 25
			},
			type : 'TextField',
			filters : {
				pfiltro : 'item.palabras_clave',
				type : 'string'
			},
			id_grupo : 1,
			grid : true,
			form : true
		}, {
			config : {
				name : 'codigo_fabrica',
				fieldLabel : 'Código de fábrica',
				allowBlank : true,
				width : '100%',
				gwidth : 100,
				maxLength : 30
			},
			type : 'TextField',
			filters : {
				pfiltro : 'item.codigo_fabrica',
				type : 'string'
			},
			id_grupo : 1,
			grid : true,
			form : true
		}, {
			config : {
				name : 'numero_serie',
				fieldLabel : 'No. de Serie',
				allowBlank : true,
				width : '100%',
				gwidth : 90,
				maxLength : 20
			},
			type : 'TextField',
			filters : {
				pfiltro : 'item.numero_serie',
				type : 'string'
			},
			id_grupo : 1,
			grid : true,
			form : true
		}, {
			config : {
				name : 'observaciones',
				fieldLabel : 'Observaciones',
				allowBlank : true,
				width : '100%',
				gwidth : 100,
				maxLength : 1000
			},
			type : 'TextArea',
			filters : {
				pfiltro : 'item.observaciones',
				type : 'string'
			},
			id_grupo : 1,
			grid : true,
			form : true
		},{
			config:{
				name: 'precio_ref',
				fieldLabel: 'Precio de Ref.',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:10
			},
			type:'NumberField',
			filters:{pfiltro:'item.precio_ref',type:'numeric'},
			id_grupo:1,
			grid:true,
			form:true
		},{
				config:{
					name: 'cantidad_max_sol',
					fieldLabel: 'Cantidad Maxima por Solicitud',
					allowBlank: false,
					anchor: '80%',
					gwidth: 100,
					maxLength:10
				},
				type:'NumberField',
				filters:{pfiltro:'item.cantidad_max_sol',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},{
			config:{
				name: 'id_moneda',
				fieldLabel: 'Moneda',
				gwidth: 100,
				renderer:function (value, p, record){return String.format('{0}', record.data['desc_moneda']);}
			},
			type:'Field',
			id_grupo:1,
			grid:true,
			form:false
		}],
		title : 'Item',
		ActSave : '../../sis_almacenes/control/Item/insertarItem',
		ActDel : '../../sis_almacenes/control/Item/eliminarItem',
		ActList : '../../sis_almacenes/control/Item/listarItem',
		id_store : 'id_item',
		fields : [{
			name : 'id_item'
		}, {
			name : 'id_clasificacion'
		}, {
			name : 'codigo',
			type : 'string'
		}, {
			name : 'nombre',
			type : 'string'
		}, {
			name : 'descripcion',
			type : 'string'
		}, {
			name : 'palabras_clave',
			type : 'string'
		}, {
			name : 'codigo_fabrica',
			type : 'string'
		}, {
			name : 'observaciones',
			type : 'string'
		}, {
			name : 'numero_serie',
			type : 'string'
		}, {
            name : 'id_unidad_medida',
            type : 'string'
        }, {
            name : 'codigo_unidad',
            type : 'string'
        },{
			name : 'precio_ref',
			type : 'numeric'
		},{
			name : 'cantidad_max_sol',
			type : 'integer'
		},{
            name : 'id_moneda',
            type : 'integer'
        }, {
            name : 'id_almacen',
            type : 'string'
        }, {
            name : 'almacenes_habilitados',
            type : 'string'
        }
        , {
            name : 'desc_moneda',
            type : 'string'
        }],
		sortInfo : {
			field : 'id_item',
			direction : 'ASC'
		},
		bdel : true,
		bsave : false,
		fwidth : 400,
		loadValoresIniciales : function() {
			Phx.vista.Item.superclass.loadValoresIniciales.call(this);
			if (this.maestro.id_clasificacion != undefined) {
				this.getComponente('id_clasificacion').setValue(this.maestro.id_clasificacion);
			}
		},
		onReloadPage : function(m) {
			this.getBoton('btnGenerarCodigo').disable();
			this.maestro = m;
			
			if (this.maestro.tipo_nodo == 'raiz' || this.maestro.tipo_nodo == 'hijo') {
				this.store.baseParams.id_clasificacion = this.maestro.id_clasificacion;
				this.tbar.items.get('b-new-' + this.idContenedor).show();
			} else if (this.maestro.tipo_nodo == 'item') {
				this.store.baseParams.id_item = this.maestro.id_item;
				this.tbar.items.get('b-new-' + this.idContenedor).hide();
			} else {
				this.tbar.items.get('b-new-' + this.idContenedor).show();
				myParams.id_clasificacion = 'null';
			}
			
			this.load({
				start : 0,
				limit : 50
			});
		},
		preparaMenu : function(n) {
			Phx.vista.Item.superclass.preparaMenu.call(this, n);
			var selectedRow = this.sm.getSelected();
			this.getBoton('btnVerReemplazos').enable();
			this.getBoton('btnVerArchivos').enable();
			if (selectedRow.data.id_clasificacion != null && selectedRow.data.codigo == "") {
				this.getBoton('btnGenerarCodigo').enable();
			} else {
				this.getBoton('btnGenerarCodigo').disable();
			}
		},
		liberaMenu : function(n) {
			Phx.vista.Item.superclass.liberaMenu.call(this, n);
			this.getBoton('btnGenerarCodigo').disable();
			this.getBoton('btnVerReemplazos').disable();
			this.getBoton('btnVerArchivos').disable();
		},
		successSave : function(resp) {
			Phx.vista.Item.superclass.successSave.call(this, resp);
			this.actualizarNodosClasificacion();
		},
		successDel : function(resp) {
			Phx.vista.Item.superclass.successDel.call(this, resp);
			this.actualizarNodosClasificacion();
		},
		actualizarNodosClasificacion : function() {
			var selectedNode = Phx.CP.getPagina(this.idContenedorPadre).sm.getSelectedNode();
			if (!selectedNode.leaf) {
				selectedNode.attributes.estado = 'restringido';
				selectedNode.reload();
			} else {
				selectedNode.parentNode.attributes.estado = 'restringido';
				selectedNode.parentNode.reload();
			}
		},
		btnGenerarCodigoHandler : function() {
			var rec = this.sm.getSelected();
			var data = rec.data;
			var global = this;
			Ext.Msg.confirm('Confirmación', '¿Está seguro de generar el código para este item?', function(btn) {
				if (btn == "yes") {
					Ext.Ajax.request({
						url : '../../sis_almacenes/control/Item/generarCodigoItem',
						params : {
							'id_item' : data.id_item,
							'id_clasificacion' : data.id_clasificacion
						},
						success : global.successSave,
						failure : global.conexionFailure,
						timeout : global.timeout,
						scope : global
					});
				}
			});
		},
		onBtnVerReemplazos : function() {
			var rec = this.sm.getSelected();
			Phx.CP.loadWindows('../../../sis_almacenes/vista/itemReemplazo/ItemReemplazo.php', 'Items de Reemplazo', {
				width : 800,
				height : 400
			}, rec.data, this.idContenedor, 'ItemReemplazo');
		},
		onBtnVerArchivos : function() {
			var rec = this.sm.getSelected();
			Phx.CP.loadWindows('../../../sis_almacenes/vista/itemArchivo/ItemArchivo.php', 'Archivos del Item', {
				width : 800,
				height : 400
			}, rec.data, this.idContenedor, 'ItemArchivo');
		},
		fheight:'60%',
    	fwidth: '60%'
	}); 
</script>