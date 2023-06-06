<?php
/**
 * @package pxP
 * @file 	repkardex.php
 * @author 	RCM
 * @date	10/07/2013
 * @description	Archivo con la interfaz de usuario que permite la ejecucion de las funcionales del sistema
 */
header("content-type:text/javascript; charset=UTF-8");
?>
<style type="text/css" rel="stylesheet">
    .x-selectable,
    .x-selectable * {
        -moz-user-select: text !important;
        -khtml-user-select: text !important;
        -webkit-user-select: text !important;
    }

    .x-grid-row td,
    .x-grid-summary-row td,
    .x-grid-cell-text,
    .x-grid-hd-text,
    .x-grid-hd,
    .x-grid-row,

    .x-grid-row,
    .x-grid-cell,
    .x-unselectable
    {
        -moz-user-select: text !important;
        -khtml-user-select: text !important;
        -webkit-user-select: text !important;
    }
</style>
<script>
    var fechaIni=document.querySelector(".fecha_ini").value;
    var fechaFin=document.querySelector(".fecha_fin").value;
	Phx.vista.repKardexItem = Ext.extend(Phx.gridInterfaz, {

        viewConfig: {
            stripeRows: false,
            getRowClass: function(record) {
                return "x-selectable";
            }
        },
		constructor : function(config) {
			this.maestro = config;
			this.description = this.maestro.item; //fRnk: titulo2 reporte genérico, fecha_ini, fecha_fin
            fechaIni=this.maestro.fecha_ini;
            fechaFin=this.maestro.fecha_fin;//console.log(fechaIni);console.log(Date.parse(fechaIni));console.log(new Date());
			if (this.maestro.mostrar_costos != 'no') {
				this.Atributos.push({
					config : {
						name : 'saldo_val',
						fieldLabel : 'Saldo Valorado',
						allowBlank : false,
						anchor : '100%',
						gwidth : 150,
						maxLength : 20
					},
					type : 'Field',
					filters : {
					    pfiltro : 'saldo_val',
						type : 'numeric'
					},
					id_grupo : 1,
					grid : true,
					form : true
				});
				this.Atributos.push({
					config : {
						name : 'costo_unitario',
						fieldLabel : 'Costo Unitario',
						allowBlank : false,
						anchor : '100%',
						gwidth : 150,
						maxLength : 20
					},
					type : 'Field',
					filters : {
					    pfiltro : 'costo_unitario',
						type : 'numeric'
					},
					id_grupo : 1,
					grid : true,
					form : true
				});

				this.Atributos.push({
					config : {
						name : 'ingreso_val',
						fieldLabel : 'Valorado Ingreso',
						allowBlank : false,
						anchor : '100%',
						gwidth : 150,
						maxLength : 20,
						renderer:function(value, p, record) {
							var aux;
							if(record.data.salida>0){
								aux='<b><font color="brown">';
							}
							else {
								aux='<b><font color="green">';
							}
							aux = aux +value+'</font></b>';
							return String.format('{0}', aux);
						}
					},
					type : 'Field',
					filters : {
					    pfiltro : 'ingreso_val',
						type : 'numeric'
					},
					id_grupo : 1,
					grid : true,
					form : true
				});
				this.Atributos.push({
					config : {
						name : 'salida_val',
						fieldLabel : 'Valorado Salida',
						allowBlank : false,
						anchor : '100%',
						gwidth : 150,
						maxLength : 20,
						renderer:function(value, p, record) {
							var aux;
							if(record.data.salida>0){
								aux='<b><font color="brown">';
							}
							else {
								aux='<b><font color="green">';
							}
							aux = aux +value+'</font></b>';
							return String.format('{0}', aux);
						}
					},
					type : 'Field',
					filters : {
					    pfiltro : 'salida_val',
						type : 'numeric'
					},
					id_grupo : 1,
					grid : true,
					form : true
				});
			}

			Phx.vista.repKardexItem.superclass.constructor.call(this, config);
			this.init();
			this.load({
				params : {
					start: 0,
					limit: 1000,
					fecha_ini:this.maestro.fecha_ini,
					fecha_fin:this.maestro.fecha_fin,
					id_item:this.maestro.id_item,
					all_alm:this.maestro.all_alm,
					id_almacen:this.maestro.id_almacen
				}
			});
            this.addButton('btnPdf', {
                text: 'Reporte',
                iconCls: 'bpdf32',
                def: 'pdf',
                disabled: false,
                handler: this.imprimirReporte,
                tooltip: '<b>Imprimir reporte</b><br/>Genera el reporte en el formato para impresión.'
            });
        },
		tam_pag:1000,
		Atributos : [{
			config : {
				labelSeparator : '',
				inputType : 'hidden',
				name : 'id'
			},
			type : 'Field',
			form : true
		},
		{
			config : {
				name : 'fecha',
				fieldLabel : 'Fecha Solicitud',
				allowBlank : false,
				anchor : '100%',
				gwidth : 90,
				maxLength : 20,
				renderer : function(value, p, record) {
					return value ? value.dateFormat('d/m/Y') : ''
				}
			},
			type : 'Field',
			filters : {
			    pfiltro : 'fecha',
				type : 'date'
			},
			id_grupo : 1,
			grid : true,
			form : true
		},
        {
            config : {
                name : 'fecha_salida',
                fieldLabel : 'Fecha Salida',
                allowBlank : false,
                anchor : '100%',
                gwidth : 90,
                maxLength : 20,
                renderer : function(value, p, record) {
                    return value ? value.dateFormat('d/m/Y') : ''
                }
            },
            type : 'Field',
            filters : {
                pfiltro : 'fecha_salida',
                type : 'date'
            },
            id_grupo : 1,
            grid : true,
            form : true
        },
		{
			config : {
				name : 'nro_mov',
				fieldLabel : 'Num.Movimiento',
				allowBlank : false,
				anchor : '100%',
				gwidth : 180,
				maxLength : 20,
				renderer:function(value, p, record) {
					var aux;
					if(record.data.salida>0){
						aux='<b><font color="brown">';
					}
					else {
						aux='<b><font color="green">';
					}
					aux = aux +value+'</font></b>';
					return String.format('{0}', aux);
				}

			},
			type : 'Field',
			filters : {
			    pfiltro : 'nro_mov',
				type : 'string'
			},
			id_grupo : 1,
			grid : true,
			form : true
		},
		{
			config : {
				name : 'almacen',
				fieldLabel : 'Almacén',
				allowBlank : false,
				anchor : '100%',
				gwidth : 150,
				maxLength : 20
			},
			type : 'Field',
			filters : {
			    pfiltro : 'almacen',
				type : 'string'
			},
			id_grupo : 1,
			grid : true,
			form : true
		},
		{
			config : {
				name : 'motivo',
				fieldLabel : 'Motivo',
				allowBlank : false,
				anchor : '100%',
				gwidth : 150,
				maxLength : 20
			},
			type : 'Field',
			filters : {
			    pfiltro : 'motivo',
				type : 'string'
			},
			id_grupo : 1,
			grid : true,
			form : true
		},
		{
			config : {
				name : 'ingreso',
				fieldLabel : 'Cantidad Ingreso',
				allowBlank : false,
				anchor : '100%',
				gwidth : 150,
				maxLength : 20,
				renderer:function(value, p, record) {
					var aux;
					if(record.data.salida>0){
						aux='<b><font color="brown">';
					}
					else {
						aux='<b><font color="green">';
					}
					aux = aux +value+'</font></b>';
					return String.format('{0}', aux);
				}
			},
			type : 'NumberField',
			filters : {
			    pfiltro : 'ingreso',
				type : 'numeric'
			},
			id_grupo : 1,
			grid : true,
			form : true
		},
		{
			config : {
				name : 'salida',
				fieldLabel : 'Cantidad salida',
				allowBlank : false,
				anchor : '100%',
				gwidth : 150,
				maxLength : 20,
				renderer:function(value, p, record) {
					var aux;
					if(record.data.salida>0){
						aux='<b><font color="brown">';
					}
					else {
						aux='<b><font color="green">';
					}
					aux = aux +value+'</font></b>';
					return String.format('{0}', aux);
				}
			},
			type : 'NumberField',
			filters : {
			    pfiltro : 'salida',
				type : 'numeric'
			},
			id_grupo : 1,
			grid : true,
			form : true
		},
		{
			config : {
				name : 'saldo',
				fieldLabel : 'Saldo Físico',
				allowBlank : false,
				anchor : '100%',
				gwidth : 150,
				maxLength : 20
			},
			type : 'Field',
			filters : {
			    pfiltro : 'saldo',
				type : 'numeric'
			},
			id_grupo : 1,
			grid : true,
			form : true
		}

		],//fRnk: se añadió el título en formato HTML, además de las fechas inicio y fin
		title : 'Kardex Item<br/><span style="font-weight: normal;font-size: 10px;">(del '+fechaIni+' al '+fechaFin+')</span>',
		ActList : '../../sis_almacenes/control/Reportes/listarKardexItem',
		id_store : 'id',
		fields : [{
			name : 'id'
		}, {
			name : 'fecha',
			type : 'date',
			dateFormat : 'Y-m-d H:i:s'
		}, {
			name : 'nro_mov',
			type : 'string'
		}, {
			name : 'almacen',
			type : 'string'
		}, {
			name : 'ingreso',
			type : 'numeric'
		}, {
			name : 'salida',
			type : 'numeric'
		}, {
			name : 'saldo',
			type : 'numeric'
		}, {
			name : 'costo_unitario',
			type : 'numeric'
		}, {
			name : 'ingreso_val',
			type : 'numeric'
		}, {
			name : 'salida_val',
			type : 'numeric'
		}, {
			name : 'saldo_val',
			type : 'numeric'
		},{
			name:'motivo',
			type:'string'
		},{
            name : 'fecha_salida',
            type : 'date',
            dateFormat : 'Y-m-d'
        }],
		sortInfo : {
			field : 'id',
			direction : 'ASC'
		},
		bdel : false,
		bnew: false,
		bedit: false,
		fwidth : '90%',
		fheight : '80%',
        imprimirReporte: function(w,resp){
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url:'../../sis_almacenes/control/Reportes/listarKardexItem',
                params:{
                    tipoReporte: 'reporte',
                    fecha_ini: this.maestro.fecha_ini,
                    id_item: this.maestro.id_item,
                    fecha_fin: this.maestro.fecha_fin,
                    all_alm: this.maestro.all_alm,
                    id_almacen: this.maestro.id_almacen,
                    item: this.maestro.item
                },
                success: this.successExport,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        }
	});
</script>
