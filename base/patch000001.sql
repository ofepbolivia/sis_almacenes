/***********************************I-SCP-JRR-ALM-1-19/11/2012****************************************/
/*
*	Author: JRR
*	Date: 11/2012
*	Description: Build the menu definition and composition
*/
--
-- Structure for table tclasificacion (OID = 688286) :
--

CREATE TABLE alm.tclasificacion (
    id_clasificacion serial NOT NULL,
    id_clasificacion_fk integer,
    codigo varchar(20),
    nombre varchar(200),
    descripcion varchar(1000),
    codigo_largo varchar(20),
    CONSTRAINT pk_tclasificacion__id_clasificacion
    PRIMARY KEY (id_clasificacion)
)
INHERITS (pxp.tbase) WITHOUT OIDS;

--
-- Structure for table talmacen (OID = 688562) :
--
CREATE TABLE alm.talmacen (
    id_almacen serial NOT NULL,
    codigo varchar(10),
    nombre varchar(100),
    localizacion varchar(100),
    CONSTRAINT pk_talmacen__id_almacen PRIMARY KEY (id_almacen)
)
INHERITS (pxp.tbase) WITHOUT OIDS;

--
-- Structure for table titem (OID = 688573) :
--
CREATE TABLE alm.titem (
    id_item serial NOT NULL,
    id_clasificacion integer,
    codigo varchar(20),
    nombre varchar(100),
    descripcion varchar(1000),
    palabras_clave varchar(1000),
    codigo_fabrica varchar(100),
    observaciones varchar(1000),
    numero_serie varchar(100),
    CONSTRAINT pk_titem__id_item PRIMARY KEY (id_item)
)
INHERITS (pxp.tbase) WITHOUT OIDS;

--
-- Structure for table talmacen_stock (OID = 688592) :
--
CREATE TABLE alm.talmacen_stock (
    id_almacen_stock serial NOT NULL,
    id_almacen integer NOT NULL,
    id_item integer,
    cantidad_min numeric(18,2),
    cantidad_alerta_amarilla numeric(18,2),
    cantidad_alerta_roja numeric(18,2),
    CONSTRAINT pk_talmacen_stock__id_almacen_stock PRIMARY KEY (id_almacen_stock)
)
INHERITS (pxp.tbase) WITHOUT OIDS;

--
-- Structure for table tmovimiento_tipo (OID = 688613) :
--
CREATE TABLE alm.tmovimiento_tipo (
    id_movimiento_tipo serial NOT NULL,
    codigo varchar(20),
    nombre varchar(100),
    CONSTRAINT pk_tmovimiento_tipo__id_movimiento_tipo
    PRIMARY KEY (id_movimiento_tipo)
)
INHERITS (pxp.tbase) WITHOUT OIDS;

--
-- Structure for table tmovimiento (OID = 688624) :
--
CREATE TABLE alm.tmovimiento (
    id_movimiento serial NOT NULL,
    id_movimiento_tipo integer,
    id_almacen integer,
    id_funcionario integer,
    id_proveedor integer,
    id_almacen_dest integer,
    fecha_mov timestamp without time zone,
    numero_mov varchar(30),
    descripcion varchar(1000),
    observaciones varchar(1000),
    estado_mov varchar(10),
    CONSTRAINT pk_tmovimiento__id_movimiento PRIMARY KEY (id_movimiento)
)
INHERITS (pxp.tbase) WITHOUT OIDS;

--
-- Structure for table tmovimiento_det (OID = 785635) :
--
CREATE TABLE alm.tmovimiento_det (
    id_movimiento_det serial NOT NULL,
    id_movimiento integer,
    id_item integer,
    cantidad numeric(18,6),
    costo_unitario numeric(18,6),
    fecha_caducidad date,
    CONSTRAINT pk_tmovimiento_det__id_movimiento_det PRIMARY KEY (id_movimiento_det)
)
INHERITS (pxp.tbase) WITHOUT OIDS;


CREATE TABLE alm.talmacen_usuario (
  id_almacen_usuario SERIAL,
  id_usuario INTEGER,
  CONSTRAINT pk_talmacen_usuario__id_almacen_usuario PRIMARY KEY(id_almacen_usuario)
) INHERITS (pxp.tbase)
WITHOUT OIDS;

ALTER TABLE alm.talmacen
  ADD COLUMN id_almacen_usuario INTEGER;


CREATE TABLE alm.talmacen_correlativo (
  id_almacen_correl SERIAL,
  id_almacen INTEGER,
  id_movimiento_tipo INTEGER,
  periodo VARCHAR,
  correl_act INTEGER DEFAULT 0,
  correl_sig INTEGER DEFAULT 1,
  CONSTRAINT pk_talmacen_correlativo__id_almacen_correl PRIMARY KEY(id_almacen_correl)
) INHERITS (pxp.tbase)
WITHOUT OIDS;

/***********************************F-SCP-JRR-ALM-1-19/11/2012****************************************/

/***********************************I-SCP-AAO-ALM-16-05/02/2013****************************************/
ALTER TABLE alm.titem
  ADD COLUMN num_por_clasificacion INTEGER;
/***********************************F-SCP-AAO-ALM-16-05/02/2013****************************************/

/***********************************I-SCP-AAO-ALM-16_2-05/02/2013****************************************/
ALTER TABLE alm.tclasificacion
  ADD COLUMN estado VARCHAR(20);
/***********************************F-SCP-AAO-ALM-16_2-05/02/2013****************************************/

/***********************************I-SCP-AAO-ALM-12-06/02/2013****************************************/
ALTER TABLE alm.talmacen_usuario
  ADD COLUMN tipo VARCHAR(20);

ALTER TABLE alm.talmacen_usuario
  ADD COLUMN id_almacen INTEGER;
/***********************************F-SCP-AAO-ALM-12-06/02/2013****************************************/

/***********************************I-SCP-AAO-ALM-14-06/02/2013****************************************/
CREATE TABLE alm.titem_reemplazo (
  id_item_reemplazo SERIAL NOT NULL,
  id_item INTEGER,
  id_item_r INTEGER,
  PRIMARY KEY(id_item_reemplazo)
) INHERITS (pxp.tbase)
WITHOUT OIDS;

ALTER TABLE alm.titem_reemplazo
  OWNER TO postgres;
/***********************************F-SCP-AAO-ALM-14-06/02/2013****************************************/

/***********************************I-SCP-AAO-ALM-15-06/02/2013****************************************/
CREATE TABLE alm.titem_archivo (
  id_item_archivo SERIAL NOT NULL,
  nombre VARCHAR(50),
  descripcion VARCHAR(150),
  extension VARCHAR(10),
  archivo BYTEA,
  id_item INTEGER,
  PRIMARY KEY(id_item_archivo)
) INHERITS (pxp.tbase)
WITHOUT OIDS;

ALTER TABLE alm.titem_archivo
  OWNER TO postgres;
/***********************************F-SCP-AAO-ALM-15-06/02/2013****************************************/

/***********************************I-SCP-AAO-ALM-23-13/02/2013****************************************/

ALTER TABLE alm.talmacen
  ADD COLUMN estado VARCHAR(15);
/***********************************F-SCP-AAO-ALM-23-13/02/2013****************************************/

/***********************************I-SCP-AAO-ALM-19-13/02/2013****************************************/

ALTER TABLE alm.tmovimiento_tipo
  ADD COLUMN tipo VARCHAR(25);
/***********************************F-SCP-AAO-ALM-19-13/02/2013****************************************/

/***********************************I-SCP-AAO-ALM-24-14/02/2013****************************************/

CREATE TABLE alm.tmetodo_val (
  id_metodo_val SERIAL NOT NULL,
  codigo VARCHAR(20),
  nombre VARCHAR(50),
  descripcion VARCHAR(150),
  PRIMARY KEY(id_metodo_val)
) INHERITS (pxp.tbase)
WITHOUT OIDS;

ALTER TABLE alm.talmacen_stock
  ADD COLUMN id_metodo_val INTEGER;
/***********************************F-SCP-AAO-ALM-24-14/02/2013****************************************/

/***********************************I-SCP-AAO-ALM-20-15/02/2013****************************************/
ALTER TABLE alm.tmovimiento
  RENAME COLUMN numero_mov TO codigo;
/***********************************F-SCP-AAO-ALM-20-15/02/2013****************************************/

/***********************************I-SCP-AAO-ALM-25-21/02/2013****************************************/
ALTER TABLE alm.tmovimiento_det
  DROP COLUMN costo_unitario;

CREATE TABLE alm.tmovimiento_det_valorado (
  id_movimiento_det_valorado SERIAL NOT NULL,
  id_movimiento_det INTEGER,
  cantidad NUMERIC(18,6),
  costo_unitario NUMERIC(18,6),
  aux_saldo_fisico NUMERIC(18,6),
  PRIMARY KEY(id_movimiento_det_valorado)
) INHERITS (pxp.tbase)
WITHOUT OIDS;

ALTER TABLE alm.tmovimiento_det_valorado
  OWNER TO postgres;
/***********************************F-SCP-AAO-ALM-25-21/02/2013****************************************/

/***********************************I-SCP-AAO-ALM-33-21/02/2013****************************************/
ALTER TABLE alm.tmovimiento_det
  ADD COLUMN costo_unitario NUMERIC(18,6);
/***********************************F-SCP-AAO-ALM-33-21/02/2013****************************************/

/***********************************I-SCP-AAO-ALM-31-23/02/2013****************************************/
ALTER TABLE alm.tmovimiento
  ADD COLUMN id_movimiento_dest INTEGER;
/***********************************F-SCP-AAO-ALM-31-23/02/2013****************************************/

/***********************************I-SCP-AAO-ALM-26-25/02/2013****************************************/
ALTER TABLE alm.talmacen
  ADD COLUMN id_departamento INTEGER;
/***********************************F-SCP-AAO-ALM-26-25/02/2013****************************************/

/***********************************I-SCP-AAO-ALM-29-25/02/2013****************************************/
ALTER TABLE alm.tmovimiento
  RENAME COLUMN id_movimiento_dest TO id_movimiento_origen;
/***********************************F-SCP-AAO-ALM-29-25/02/2013****************************************/

/***********************************I-SCP-AAO-ALM-28-26/02/2013****************************************/
ALTER TABLE alm.tmovimiento_det_valorado
  ADD COLUMN id_mov_det_val_origen INTEGER;
/***********************************F-SCP-AAO-ALM-28-26/02/2013****************************************/

/***********************************I-SCP-AAO-ALM-34-01/03/2013****************************************/
ALTER TABLE alm.titem
  ADD COLUMN id_unidad_medida INTEGER;
/***********************************F-SCP-AAO-ALM-34-01/03/2013****************************************/

/***********************************I-SCP-AAO-ALM-35-04/03/2013****************************************/
ALTER TABLE alm.talmacen
  ALTER COLUMN codigo TYPE VARCHAR(20);

ALTER TABLE alm.titem
  ALTER COLUMN codigo TYPE VARCHAR(30);
/***********************************F-SCP-AAO-ALM-35-04/03/2013****************************************/

/***********************************I-SCP-AAO-ALM-41-05/03/2013*****************************************/
ALTER TABLE alm.titem_archivo
  ALTER COLUMN descripcion TYPE VARCHAR(1000);

ALTER TABLE alm.tmetodo_val
  ALTER COLUMN descripcion TYPE VARCHAR(1000);
/***********************************F-SCP-AAO-ALM-41-05/03/2013*****************************************/

/***********************************I-SCP-AAO-ALM-60-14/03/2013*****************************************/
ALTER TABLE alm.tmovimiento_tipo
  ADD COLUMN read_only BOOLEAN;

ALTER TABLE alm.tmovimiento_tipo
  ALTER COLUMN read_only SET DEFAULT FALSE;

ALTER TABLE alm.tmetodo_val
  ADD COLUMN read_only BOOLEAN;

ALTER TABLE alm.tmetodo_val
  ALTER COLUMN read_only SET DEFAULT FALSE;
/***********************************F-SCP-AAO-ALM-60-14/03/2013*****************************************/

/***********************************I-SCP-AAO-ALM-45-14/03/2013*****************************************/
CREATE TABLE alm.tinventario (
  id_inventario SERIAL,
  id_almacen INTEGER NOT NULL,
  id_usuario_resp INTEGER NOT NULL,
  fecha_inv_planif TIMESTAMP WITHOUT TIME ZONE,
  fecha_inv_ejec TIMESTAMP WITHOUT TIME ZONE,
  observaciones VARCHAR(1000),
  completo VARCHAR(2) NOT NULL,
  estado VARCHAR(20),
  CONSTRAINT tinventario_pkey PRIMARY KEY(id_inventario)
) INHERITS (pxp.tbase)
WITHOUT OIDS;

ALTER TABLE alm.tinventario
  OWNER TO postgres;
/***********************************F-SCP-AAO-ALM-45-14/03/2013*****************************************/

/***********************************I-SCP-AAO-ALM-45-15/03/2013*****************************************/

CREATE TABLE alm.tinventario_det (
  id_inventario_det SERIAL NOT NULL,
  id_inventario INTEGER,
  id_item INTEGER,
  cantidad_sistema NUMERIC(18,2),
  cantidad_real NUMERIC(18,2),
  diferencia NUMERIC(18,2),
  observaciones VARCHAR(1000),
  PRIMARY KEY(id_inventario_det)
) INHERITS (pxp.tbase)
WITHOUT OIDS;

ALTER TABLE alm.tinventario_det
  OWNER TO postgres;

/***********************************F-SCP-AAO-ALM-45-15/03/2013*****************************************/

/***********************************I-SCP-AAO-ALM-9-18/03/2013*****************************************/

ALTER TABLE alm.talmacen_stock
  ALTER COLUMN id_item SET NOT NULL;

ALTER TABLE alm.titem_reemplazo
  ALTER COLUMN id_item SET NOT NULL;

ALTER TABLE alm.titem_reemplazo
  ALTER COLUMN id_item_r SET NOT NULL;

ALTER TABLE alm.tmovimiento_det
  ALTER COLUMN id_item SET NOT NULL;
/***********************************F-SCP-AAO-ALM-9-18/03/2013*****************************************/

/***********************************I-SCP-AAO-ALM-70-21/03/2013*****************************************/
ALTER TABLE alm.tmovimiento_det
  ADD COLUMN cantidad_solicitada numeric(18,6);
/***********************************F-SCP-AAO-ALM-70-21/03/2013*****************************************/

/***********************************I-SCP-RCM-ALM-79-19/06/2013*****************************************/
CREATE TABLE alm.tperiodo(
	id_periodo serial not null,
	periodo date,
	fecha_ini date,
	fecha_fin date,
	CONSTRAINT tperiodo__id_periodo PRIMARY KEY(id_periodo)
) INHERITS (pxp.tbase)
WITHOUT OIDS;

CREATE TABLE alm.tperiodo_log(
	id_periodo_log serial not null,
	id_periodo integer not null,
	estado_reg_ant varchar(15),
	CONSTRAINT tperiodo_log__id_periodo_log PRIMARY KEY(id_periodo_log)
) INHERITS (pxp.tbase)
WITHOUT OIDS;
/***********************************F-SCP-RCM-ALM-79-19/06/2013*****************************************/

/***********************************I-SCP-GSS-ALM-69-05/07/2013*****************************************/

ALTER TABLE alm.tmovimiento
  ADD COLUMN id_proceso_macro INTEGER;

ALTER TABLE alm.tmovimiento
  ADD COLUMN id_estado_wf INTEGER;

ALTER TABLE alm.tmovimiento
  ADD COLUMN id_proceso_wf INTEGER;

ALTER TABLE alm.tmovimiento_tipo
  ADD COLUMN id_proceso_macro INTEGER;

/***********************************F-SCP-GSS-ALM-69-05/07/2013*****************************************/

/***********************************I-SCP-RCM-ALM-82-18/07/2013*****************************************/
CREATE TABLE alm.tpreingreso(
	id_preingreso serial not null,
	id_cotizacion integer not null,
	id_almacen integer,
	CONSTRAINT tpreingreso__id_preingreso PRIMARY KEY(id_preingreso)
) INHERITS (pxp.tbase)
WITHOUT OIDS;

CREATE TABLE alm.tpreingreso_det(
	id_preingreso_det serial not null,
	id_preingreso integer not null,
	id_cotizacion_det integer,
	id_item integer,
	id_almacen integer,
	cantidad numeric(18,2),
	precio_compra numeric(18,2),
	CONSTRAINT tpreingreso_det__id_preingreso_det PRIMARY KEY(id_preingreso_det)
) INHERITS (pxp.tbase)
WITHOUT OIDS;

CREATE TABLE alm.titem_concepto(
	id_item_concepto serial not null,
	id_item integer not null,
	id_concepto_ingas integer not null,
	CONSTRAINT titem_concepto__id_item_concepto PRIMARY KEY(id_item_concepto)
) INHERITS (pxp.tbase)
WITHOUT OIDS;
/***********************************F-SCP-RCM-ALM-82-18/07/2013*****************************************/

/***********************************I-SCP-GSS-ALM-90-25/07/2013*****************************************/

ALTER TABLE alm.tmovimiento_det
  ADD COLUMN observaciones VARCHAR(1000);

/***********************************F-SCP-GSS-ALM-90-25/07/2013*****************************************/

/***********************************I-SCP-GSS-ALM-86-26/07/2013*****************************************/

ALTER TABLE alm.tinventario
  ADD COLUMN id_usuario_asis INTEGER;

/***********************************F-SCP-GSS-ALM-86-26/07/2013*****************************************/


/***********************************I-SCP-GSS-ALM-87-26/07/2013*****************************************/

CREATE TABLE alm.tmovimiento_tipo_item (
  id_movimiento_tipo_item SERIAL,
  id_movimiento_tipo INTEGER,
  id_item INTEGER,
  CONSTRAINT pk_tmovimiento_tipo_item__id_movimiento_tipo_item PRIMARY KEY(id_movimiento_tipo_item)
) INHERITS(pxp.tbase)
WITHOUT OIDS;

/***********************************F-SCP-GSS-ALM-87-26/07/2013*****************************************/

/***********************************I-SCP-RCM-ALM-87-21/08/2013*****************************************/
alter table alm.tmovimiento_tipo_item
add column id_clasificacion integer;
/***********************************F-SCP-RCM-ALM-87-21/08/2013*****************************************/

/***********************************I-SCP-RCM-ALM-95-22/08/2013*****************************************/
CREATE TABLE alm.tmovimiento_tipo_uo (
  id_movimiento_tipo_uo SERIAL,
  id_movimiento_tipo INTEGER,
  id_uo INTEGER,
  CONSTRAINT pk_tmovimiento_tipo_uo__id_movimiento_tipo_uo PRIMARY KEY(id_movimiento_tipo_uo)
) INHERITS(pxp.tbase)
WITHOUT OIDS;
/***********************************F-SCP-RCM-ALM-95-22/08/2013*****************************************/

/***********************************I-SCP-RCM-ALM-82-01/10/2013*****************************************/
ALTER TABLE alm.tpreingreso
  ADD COLUMN id_depto INTEGER;
ALTER TABLE alm.tpreingreso
  ADD COLUMN id_estado_wf INTEGER;
ALTER TABLE alm.tpreingreso
  ADD COLUMN id_proceso_wf INTEGER;
ALTER TABLE alm.tpreingreso
  ADD COLUMN estado varchar(50);
ALTER TABLE alm.tpreingreso
  ADD COLUMN id_moneda INTEGER;

ALTER TABLE alm.tpreingreso_det
  ADD COLUMN id_depto INTEGER;

ALTER TABLE alm.tpreingreso_det
  ADD COLUMN id_clasificacion INTEGER;

CREATE TABLE alm.titem_clasif_ingas (
  id_item_clasif_ingas SERIAL,
  id_concepto_ingas INTEGER,
  id_item INTEGER,
  id_clasificacion INTEGER,
  contador integer,
  CONSTRAINT pk_titem_clasif_ingas__id_item_clasif_ingas PRIMARY KEY(id_item_clasif_ingas)
) INHERITS(pxp.tbase)
WITHOUT OIDS;

drop table alm.titem_concepto;

ALTER TABLE alm.tpreingreso
  ADD COLUMN tipo VARCHAR(15) NOT NULL;

ALTER TABLE alm.tpreingreso_det
  ADD COLUMN sw_generar VARCHAR(2);

ALTER TABLE alm.tpreingreso_det
  ALTER COLUMN sw_generar SET DEFAULT 'no';

ALTER TABLE alm.tpreingreso
  ADD COLUMN descripcion VARCHAR(1000);

ALTER TABLE alm.tpreingreso_det
  ADD COLUMN observaciones VARCHAR(1000);

ALTER TABLE alm.tmovimiento
  ADD COLUMN id_preingreso INTEGER;

  ALTER TABLE alm.tpreingreso_det
  RENAME COLUMN cantidad TO cantidad_det;
/***********************************F-SCP-RCM-ALM-82-01/10/2013*****************************************/

/***********************************I-SCP-RCM-ALM-0-10/10/2013*****************************************/
alter table alm.titem
add column precio_ref numeric(18,2);

alter table alm.titem
add column id_moneda integer;

/***********************************F-SCP-RCM-ALM-0-10/10/2013*****************************************/

/***********************************I-SCP-RCM-ALM-0-17/10/2013*****************************************/
CREATE TABLE alm.tsalida_grupo (
  id_salida_grupo SERIAL,
  id_almacen integer,
  id_movimiento_tipo integer,
  descripcion varchar(1000),
  observaciones varchar(1000),
  estado varchar(15),
  fecha date,
  CONSTRAINT pk_tsalida_grupo__id_salida_grupo PRIMARY KEY(id_salida_grupo)
) INHERITS(pxp.tbase)
WITHOUT OIDS;

CREATE TABLE alm.tsalida_grupo_item (
  id_salida_grupo_item SERIAL,
  id_salida_grupo integer,
  id_item integer,
  cantidad_sol numeric(18,2),
  observaciones varchar(1000),
  CONSTRAINT pk_tsalida_grupo_item__id_salida_grupo_item PRIMARY KEY(id_salida_grupo_item)
) INHERITS(pxp.tbase)
WITHOUT OIDS;

CREATE TABLE alm.tsalida_grupo_fun (
  id_salida_grupo_fun SERIAL,
  id_salida_grupo_item integer,
  id_funcionario integer,
  cantidad_sol numeric(18,2),
  observaciones varchar(1000),
  CONSTRAINT pk_tsalida_grupo_fun__id_salida_grupo_fun PRIMARY KEY(id_salida_grupo_fun),
  CONSTRAINT uq_tsalida_grupo_fun__id_funcionario UNIQUE (id_salida_grupo_item,id_funcionario)
) INHERITS(pxp.tbase)
WITHOUT OIDS;

alter table alm.tmovimiento
add column id_salida_grupo integer;

alter table alm.tmovimiento
add column id_int_comprobante integer;

CREATE TABLE alm.tmovimiento_grupo (
  id_movimiento_grupo SERIAL,
  id_almacen integer,
  id_int_comprobante integer,
  id_depto_conta INTEGER,
  descripcion varchar(1000),
  estado varchar(15),
  fecha_ini date,
  fecha_fin date,
  CONSTRAINT pk_tmovimiento_grupo__id_movimiento_grupo PRIMARY KEY(id_movimiento_grupo)
) INHERITS(pxp.tbase)
WITHOUT OIDS;

CREATE TABLE alm.tmovimiento_grupo_det (
  id_movimiento_grupo_det SERIAL,
  id_movimiento_grupo integer,
  id_movimiento integer,
  CONSTRAINT pk_tmovimiento_grupo_det__id_movimiento_grupo_det PRIMARY KEY(id_movimiento_grupo_det)
) INHERITS(pxp.tbase)
WITHOUT OIDS;

alter table alm.tpreingreso
add column id_depto_conta integer;

ALTER TABLE alm.tmovimiento
  ALTER COLUMN estado_mov TYPE VARCHAR(20);
/***********************************F-SCP-RCM-ALM-0-17/10/2013*****************************************/

/***********************************I-SCP-RCM-ALM-0-31/10/2013*****************************************/
ALTER TABLE alm.tmovimiento
  ADD COLUMN id_depto_conta integer;
ALTER TABLE alm.tmovimiento_det
  ADD COLUMN id_concepto_ingas integer;

/***********************************F-SCP-RCM-ALM-0-31/10/2013*****************************************/

/***********************************I-SCP-RCM-ALM-0-20/11/2013*****************************************/
ALTER TABLE alm.tmovimiento
  ALTER COLUMN fecha_mod DROP DEFAULT;
/***********************************F-SCP-RCM-ALM-0-20/11/2013*****************************************/

/***********************************I-SCP-RCM-ALM-0-30/12/2013*****************************************/
CREATE TABLE alm.talmacen_gestion (
  id_almacen_gestion SERIAL,
  id_almacen INTEGER,
  id_gestion INTEGER,
  estado VARCHAR,
  PRIMARY KEY(id_almacen_gestion)
) INHERITS (pxp.tbase)
WITHOUT OIDS;
/***********************************F-SCP-RCM-ALM-0-30/12/2013*****************************************/

/***********************************I-SCP-RCM-ALM-0-31/12/2013*****************************************/
CREATE TABLE alm.talmacen_gestion_log (
  id_almacen_gestion_log SERIAL,
  id_almacen_gestion INTEGER,
  estado VARCHAR,
  PRIMARY KEY(id_almacen_gestion_log)
) INHERITS (pxp.tbase)
WITHOUT OIDS;

alter table alm.talmacen_gestion
add constraint uq_talmacen_gestion__id_almacen__id_gestion unique (id_almacen,id_gestion);

ALTER TABLE alm.tmovimiento
  ADD COLUMN id_almacen_gestion_log INTEGER;
/***********************************F-SCP-RCM-ALM-0-31/12/2013*****************************************/

/***********************************I-SCP-JRR-ALM-0-21/03/2015*****************************************/

ALTER TABLE alm.tmovimiento_det
  ADD COLUMN id_movimiento_det_ingreso INTEGER;

/***********************************F-SCP-JRR-ALM-0-21/03/2015*****************************************/

/***********************************I-SCP-RCM-ALM-0-03/05/2015*****************************************/
ALTER TABLE alm.tpreingreso_det
  ADD COLUMN estado VARCHAR(10);
COMMENT ON COLUMN alm.tpreingreso_det.estado
IS 'Indica si el registro es creado desde adquisiciones al generar el preingreso (''orig''), si es modificado desde preingreso (''mod'')';
/***********************************F-SCP-RCM-ALM-0-03/05/2015*****************************************/

/***********************************I-SCP-JRR-ALM-0-04/08/2015*****************************************/
ALTER TABLE alm.tpreingreso_det
  ADD COLUMN nombre VARCHAR(255);

ALTER TABLE alm.tpreingreso_det
  ADD COLUMN descripcion TEXT;

ALTER TABLE alm.tpreingreso_det
  ADD COLUMN precio_compra_87 NUMERIC(18,2);

ALTER TABLE alm.tpreingreso_det
  ADD COLUMN id_lugar INTEGER;

ALTER TABLE alm.tpreingreso_det
  ADD COLUMN ubicacion VARCHAR(255);

/***********************************F-SCP-JRR-ALM-0-04/08/2015*****************************************/

/***********************************I-SCP-JRR-ALM-0-11/08/2015*****************************************/

ALTER TABLE alm.tpreingreso
  ADD COLUMN c31 VARCHAR(50);

ALTER TABLE alm.tpreingreso_det
  ADD COLUMN c31 VARCHAR(50);

ALTER TABLE alm.tpreingreso_det
  ADD COLUMN fecha_conformidad DATE;


/***********************************F-SCP-JRR-ALM-0-11/08/2015*****************************************/


/***********************************I-SCP-JRR-ALM-0-16/09/2015*****************************************/

ALTER TABLE alm.tpreingreso_det
  ADD COLUMN fecha_compra DATE;

/***********************************F-SCP-JRR-ALM-0-16/09/2015*****************************************/


/***********************************I-SCP-JRR-ALM-0-08/10/2015*****************************************/

ALTER TABLE alm.talmacen
  ADD COLUMN id_metodo_val INTEGER;

/***********************************F-SCP-JRR-ALM-0-08/10/2015*****************************************/

/***********************************I-SCP-JRR-ALM-0-23/06/2016*****************************************/

CREATE TYPE alm.detalle_movimiento AS (
  codigo_item VARCHAR(50),
  cantidad NUMERIC(18,6)
);

/***********************************F-SCP-JRR-ALM-0-23/06/2016*****************************************/

/***********************************I-SCP-GSS-ALM-1-13/07/2016*****************************************/

CREATE TABLE alm.tmovimiento_tipo_almacen (
  id_movimiento_tipo_almacen SERIAL,
  id_movimiento_tipo INTEGER NOT NULL,
  id_almacen INTEGER NOT NULL,
  PRIMARY KEY(id_movimiento_tipo_almacen)
) INHERITS (pxp.tbase) WITHOUT OIDS;

/***********************************F-SCP-GSS-ALM-1-13/07/2016*****************************************/


/***********************************I-SCP-GSS-ALM-1-14/11/2016*****************************************/

ALTER TABLE alm.titem
  ADD COLUMN cantidad_max_sol INTEGER;

COMMENT ON COLUMN alm.titem.cantidad_max_sol
IS 'cantidad maxima por solicitud';

/***********************************F-SCP-GSS-ALM-1-14/11/2016*****************************************/

/***********************************I-SCP-GSS-ALM-0-08/05/2017*****************************************/

ALTER TABLE alm.tmovimiento
  ADD COLUMN codigo_tran VARCHAR(22);

COMMENT ON COLUMN alm.tmovimiento.codigo_tran
IS 'codigo transaccion de sistema dotaciones';

/***********************************F-SCP-GSS-ALM-0-08/05/2017*****************************************/

/***********************************I-SCP-GSS-ALM-0-18/05/2017*****************************************/

CREATE TABLE alm.titem_concepto_ingas (
  id_item_concepto_ingas SERIAL,
  id_item INTEGER,
  id_concepto_ingas INTEGER,
  CONSTRAINT titem_concepto_ingas_pkey PRIMARY KEY(id_item_concepto_ingas)
) INHERITS (pxp.tbase)

WITH (oids = false);

/***********************************F-SCP-GSS-ALM-0-18/05/2017*****************************************/

/***********************************I-SCP-IRVA-ALM-0-12/10/2018*****************************************/
ALTER TABLE alm.tpreingreso_det
  ADD COLUMN id_unidad_medida INTEGER;

ALTER TABLE alm.tpreingreso_det
  ADD COLUMN vida_util_original INTEGER;


ALTER TABLE alm.tpreingreso_det
  ADD COLUMN nro_serie VARCHAR(50);

ALTER TABLE alm.tpreingreso_det
  ADD COLUMN marca VARCHAR(200);

ALTER TABLE alm.tpreingreso_det
  ADD COLUMN id_cat_estado_fun INTEGER;

ALTER TABLE alm.tpreingreso_det
  ADD COLUMN id_deposito INTEGER;

ALTER TABLE alm.tpreingreso_det
  ADD COLUMN id_oficina INTEGER;

ALTER TABLE alm.tpreingreso_det
  ADD COLUMN id_proveedor INTEGER;

ALTER TABLE alm.tpreingreso_det
  ADD COLUMN documento VARCHAR(100);

ALTER TABLE alm.tpreingreso_det
  ADD COLUMN id_cat_estado_compra INTEGER;

ALTER TABLE alm.tpreingreso_det
  ADD COLUMN fecha_cbte_asociado DATE;

ALTER TABLE alm.tpreingreso_det
  ADD COLUMN monto_compra NUMERIC(18,2);

ALTER TABLE alm.tpreingreso_det
  ADD COLUMN id_proyecto INTEGER;

ALTER TABLE alm.tpreingreso_det
  ADD COLUMN tramite_compra VARCHAR(100);

ALTER TABLE alm.tpreingreso_det
  ADD COLUMN subtipo VARCHAR(50);

ALTER TABLE alm.tpreingreso_det
  ADD COLUMN movimiento VARCHAR(50);

/***********************************F-SCP-IRVA-ALM-0-12/10/2018*****************************************/


/***********************************I-SCP-IRVA-ALM-0-23/10/2018*****************************************/
ALTER TABLE alm.tpreingreso_det
ADD COLUMN id_uo INTEGER;
/***********************************F-SCP-IRVA-ALM-0-23/10/2018*****************************************/

/***********************************I-SCP-FEA-ALM-0-7/11/2018*****************************************/
ALTER TABLE alm.tmovimiento
ADD COLUMN id_plantilla INTEGER;
/***********************************F-SCP-FEA-ALM-0-7/11/2018*****************************************/

/***********************************I-SCP-MAY-ALM-0-13/11/2018*****************************************/
ALTER TABLE alm.tpreingreso_det
  ADD COLUMN fecha_inicio DATE;

ALTER TABLE alm.tpreingreso_det
  ADD COLUMN fecha_fin DATE;
/***********************************F-SCP-MAY-ALM-0-13/11/2018*****************************************/

/***********************************I-SCP-ALAN-ALM-0-02/10/2019*****************************************/
ALTER TABLE alm.tmovimiento_det
  ADD COLUMN estado_dotacion VARCHAR;

/***********************************F-SCP-ALAN-ALM-0-02/10/2019*****************************************/

/***********************************I-SCP-ALAN-ALM-0-29/11/2019*****************************************/
--patch esquema alm. tablas

COMMENT ON TABLE alm.talmacen
IS 'Esta Tabla contiene informacion basica de los distintos Almacenes como ser su codigo y ubicacion entre otros';
COMMENT ON COLUMN alm.talmacen.id_metodo_val
IS 'este es el identificador del tipo evaluacion de inventarios';
COMMENT ON COLUMN alm.talmacen.id_departamento
IS 'este es el identificador de a que departamento de la empresa pertenece el almacen ej:Almacen de Servicios  a Bordo SRZ';
COMMENT ON TABLE alm.talmacen_gestion
IS 'esta tabla contiene informacion del estado (abierto,cerrado,registrado) de un almacen en una gestion especifica';
COMMENT ON COLUMN alm.talmacen_gestion.estado
IS 'estado del almacen en la gestion puede ser abierta, cerrado, registrado';
COMMENT ON COLUMN alm.talmacen_gestion.id_gestion
IS 'es el identificador de la gestion en la que se encuentra el almacen';
COMMENT ON TABLE alm.talmacen_stock
IS 'Esta tabla contiene informacion del stock en tres niveles (minimo, alerta_amarilla y alerta roja) por item y almacen correspondiente';
COMMENT ON COLUMN alm.talmacen_stock.id_metodo_val
IS 'identificador del metodo valorado del almacen ej: PEPS';
COMMENT ON COLUMN alm.talmacen_stock.id_item
IS 'identificador del item para el stock en su respectivo almacen';
COMMENT ON COLUMN alm.talmacen_stock.id_almacen
IS 'identificador del almacen del que proviene el item en stock correspondiente';
COMMENT ON TABLE alm.talmacen_usuario
IS 'esta tabla contiene informacion de la responsabilidad (asistente, responsable) de un usuario en almacen';
COMMENT ON TABLE alm.talmacen_usuario.tipo
IS 'responsabilidad del usuario en su almacen correspondiente';
COMMENT ON TABLE alm.talmacen_usuario.id_usuario
IS 'identificador del usuario asignado a un almacen';
COMMENT ON TABLE alm.tclasificacion
IS 'Esta tabla contiene informacion de los codigos de los items para el buscador de materiales';
COMMENT ON TABLE alm.tclasificacion.codigo_largo
IS 'contiene informacion del codigo de un item o categoria o sub categoria al que pertenece un item';
COMMENT ON TABLE alm.titem
IS 'esta tabla contiene informacion basica de los items que puede existir en un almacen como ser nombre,descripcion,clasificacion';

COMMENT ON COLUMN alm.titem.codigo
IS 'codigo del item';
COMMENT ON COLUMN alm.titem.nombre
IS 'nombre del item';
COMMENT ON COLUMN alm.titem.descripcion
IS 'descripcion del item';
COMMENT ON COLUMN alm.titem.cantidad_max_sol
IS 'cantidad maxima por solicitud';

COMMENT ON TABLE alm.titem_concepto_ingas
IS 'esta tabla relaciona el tipo de concepto de la tabla param.tconcepto_ingas con un item';
COMMENT ON COLUMN alm.titem_concepto_ingas.id_item
IS 'identificador del item';
COMMENT ON COLUMN alm.titem_concepto_ingas.id_concepto_ingas
IS 'identificador del concepto de ingreso gasto de un item';

COMMENT ON TABLE alm.tmetodo_val
IS 'esta tabla contiene informacion del tipo de control de inventarios para un almacen';
COMMENT ON COLUMN alm.tmetodo_val.codigo
IS 'codigo del metodo de control de inventario';
COMMENT ON COLUMN alm.tmetodo_val.nombre
IS 'nombre del metodo de control de inventario';
COMMENT ON COLUMN alm.tmetodo_val.descripcion
IS 'descripcion del metodo de control de inventario';

COMMENT ON TABLE alm.tmovimiento
IS 'esta tabla contiene informacion de un movimiento de almacen asi como el estado en el flujo de trabajo correspondiente';
COMMENT ON COLUMN alm.tmovimiento.id_movimiento_tipo
IS 'identificador del tipo de movimiento';
COMMENT ON COLUMN alm.tmovimiento.id_almacen
IS 'identificador del almacen';
COMMENT ON COLUMN alm.tmovimiento.id_funcionario
IS 'identificador del funcionario que hace el movimiento';
COMMENT ON COLUMN alm.tmovimiento.id_proveedor
IS 'identificador de un proveedor';
COMMENT ON COLUMN alm.tmovimiento.id_almacen_dest
IS 'identificador del almacen destino';
COMMENT ON COLUMN alm.tmovimiento.fecha_mov
IS 'fecha de inicio del movimiento';
COMMENT ON COLUMN alm.tmovimiento.codigo
IS 'codigo del movimiento';
COMMENT ON COLUMN alm.tmovimiento.descripcion
IS 'descripcion del movimiento';
COMMENT ON COLUMN alm.tmovimiento.estado_mov
IS 'estado del movimiento en el flujo de trabajo';
COMMENT ON COLUMN alm.tmovimiento.id_estado_wf
IS 'identificador del estado en el flujo de trabajo';
COMMENT ON COLUMN alm.tmovimiento.id_proceso_wf
IS 'identificador del proceso en el flujo de trabajo';
COMMENT ON COLUMN alm.tmovimiento.id_depto_conta
IS 'identificador del departamento de contabilidad';
COMMENT ON COLUMN alm.tmovimiento.codigo_tran
IS 'codigo de transaccion para el uso de servicios web';

COMMENT ON TABLE alm.tmovimiento_det
IS 'esta tabla contiene los detalles de un movimiento de almacen ';
COMMENT ON COLUMN alm.tmovimiento_det.id_movimiento
IS 'identificador del movimiento';
COMMENT ON COLUMN alm.tmovimiento_det.id_item
IS 'identificador de un item';
COMMENT ON COLUMN alm.tmovimiento_det.cantidad
IS 'cantidad de un item';
COMMENT ON COLUMN alm.tmovimiento_det.costo_unitario
IS 'costo unitario de un item';
COMMENT ON COLUMN alm.tmovimiento_det.id_concepto_ingas
IS 'identificador del concepto del ingreso o gasto';

COMMENT ON TABLE alm.tmovimiento_det_valorado
IS 'esta tabla tiene informacion valorada del detalle del movimiento';
COMMENT ON COLUMN alm.tmovimiento_det_valorado.id_movimiento_det
IS 'identificador del detalle del movimiento';

COMMENT ON TABLE alm.tmovimiento_tipo
IS 'esta tabla contiene informacion del tipo de movimiento que se puede generar';
COMMENT ON COLUMN alm.tmovimiento_tipo.codigo
IS 'codigo del tipo de movimiento';
COMMENT ON COLUMN alm.tmovimiento_tipo.nombre
IS 'nombre del tipo de movimiento';
COMMENT ON COLUMN alm.tmovimiento_tipo.tipo
IS 'tipo de movimiento ingreso-salida';
COMMENT ON COLUMN alm.tmovimiento_tipo.id_proceso_macro
IS 'identificador del proceso macro del flujo de trabajo';

COMMENT ON TABLE alm.tmovimiento_tipo_almacen
IS 'esta tabla cruza informacion de que tipos de movimientos realiza un almacen';
COMMENT ON COLUMN alm.tmovimiento_tipo_almacen.id_movimiento_tipo
IS 'identificador del tipo de movimiento';
COMMENT ON COLUMN alm.tmovimiento_tipo_almacen.id_almacen
IS 'identificador del almacen';

COMMENT ON TABLE alm.tmovimiento_tipo_item
IS 'esta tabla cruza informacion del tipo de movimiento, item y clasificacion del item';
COMMENT ON COLUMN alm.tmovimiento_tipo_item.id_movimiento_tipo
IS 'identificador del tipo de movimiento';
COMMENT ON COLUMN alm.tmovimiento_tipo_item.id_item
IS 'identificador del item';
COMMENT ON COLUMN alm.tmovimiento_tipo_item.id_clasificacion
IS 'identificador de la clasificacion de clase de un item o item';

COMMENT ON TABLE alm.tmovimiento_tipo_uo
IS 'esta tabla cruza informacion del tipo de movimiento con la unidad operativa de la organizacion';
COMMENT ON COLUMN alm.tmovimiento_tipo_uo.id_movimiento_tipo
IS 'identificador del tipo de movimiento';
COMMENT ON COLUMN alm.tmovimiento_tipo_uo.id_uo
IS 'identificador de la unidad operativa orga.tuo';

COMMENT ON TABLE alm.tpreingreso
IS 'esta tabla cruza informacion con el almacen, cotizaciones de adquisiciones, departamento y departamento de contabilidad, para el preingreso de un item';
COMMENT ON COLUMN alm.tpreingreso.id_cotizacion
IS 'identificador de una cotizacion adq.tcotizacion';
COMMENT ON COLUMN alm.tpreingreso.id_almacen
IS 'identificador de un almacen';
COMMENT ON COLUMN alm.tpreingreso.id_depto_conta
IS 'identificador del departamento de contabilidad';

COMMENT ON TABLE alm.tpreingreso_det
IS 'esta tabla contiene la informacion de un preingreso de un item a un determinado almacen';

COMMENT ON COLUMN alm.tpreingreso_det.id_preingreso
IS 'identificador del preingreso';

COMMENT ON COLUMN alm.tpreingreso_det.id_cotizacion_det
IS 'identificador de la cotizacion por detalle adq.tcotizacion_det';
COMMENT ON COLUMN alm.tpreingreso_det.id_item
IS 'identificador del item';
COMMENT ON COLUMN alm.tpreingreso_det.id_almacen
IS 'identificador del almacen';
COMMENT ON COLUMN alm.tpreingreso_det.cantidad_det
IS 'detalle de cantidad de un preingreso de un item';
COMMENT ON COLUMN alm.tpreingreso_det.precio_compra
IS 'precio de compra del item';
COMMENT ON COLUMN alm.tpreingreso_det.estado
IS 'Indica si el registro es creado desde adquisiciones al generar el preingreso (''orig''), si es modificado desde preingreso (''mod'')';
COMMENT ON COLUMN alm.tpreingreso_det.nombre
IS 'nombre de la compra del item del pre ingreso';
COMMENT ON COLUMN alm.tpreingreso_det.descripcion
IS 'descripcion de la compra';
COMMENT ON COLUMN alm.tpreingreso_det.ubicacion
IS 'ubicacion destino';

COMMENT ON TABLE alm.tsaldo_fisico_item
IS 'esta tabla contiene informacion del saldo fisico de un item en un almacen';
COMMENT ON COLUMN alm.tsaldo_fisico_item.id_item
IS 'identificador del item';
COMMENT ON COLUMN alm.tsaldo_fisico_item.id_almacen
IS 'identificador del almacen';
COMMENT ON COLUMN alm.tsaldo_fisico_item.fisico
IS 'saldo fisico';

COMMENT ON TABLE alm.tsaldo_valorado_item
IS 'esta tabla contiene informacion del valor del saldo fisico de un item en un almacen';
COMMENT ON COLUMN alm.tsaldo_valorado_item.id_item
IS 'identificador del item';
COMMENT ON COLUMN alm.tsaldo_valorado_item.id_almacen
IS 'identificador del almacen';
COMMENT ON COLUMN alm.tsaldo_valorado_item.valorado
IS 'valor del saldo fisico';

COMMENT ON TABLE alm.ttemporal
IS 'esta tabla contiene informacion temporal de precio y cantidad de un item con su respectivo codigo';
COMMENT ON COLUMN alm.ttemporal.codigo
IS 'codigo del item';
COMMENT ON COLUMN alm.ttemporal.cantidad
IS 'cantidad del item';
COMMENT ON COLUMN alm.ttemporal.precio
IS 'precio del item x cantidad';
COMMENT ON COLUMN alm.ttemporal.id_item
IS 'identificador del item';


--alm. funciones

COMMENT ON FUNCTION alm.f_actualizar_saldos_inventario(p_id_inventario integer)
IS 'actualiza los saldos de los items por inventario';
COMMENT ON FUNCTION alm.f_cbte_validado_ingreso(p_id_usuario integer, p_id_int_comprobante integer)
IS 'Función para finalización de ingresos después de la validación del comprobante';
COMMENT ON FUNCTION alm.f_codigo_clasificaciones_recursivo(v_id_clasificacion integer)
IS 'esta funcion clasifica el codigo de un item recursivamente';
COMMENT ON FUNCTION alm.f_existencias_almacen_sel(p_id_almacen integer, p_fecha_hasta date, p_condicion varchar, p_filtro varchar)
IS 'Función para listar las existencias de los materiales';
COMMENT ON FUNCTION alm.f_finalizar_movimientos_intermedios()
IS 'funcion que finaliza movimientos en el flujo de trabajo con el mensaje Proceso anulado por cierre de Gestión';
COMMENT ON FUNCTION alm.f_fun_inicio_movimiento_wf(p_id_usuario integer, p_id_usuario_ai integer, p_usuario_ai varchar, p_id_estado_wf integer, p_id_proceso_wf integer, p_codigo_estado varchar)
IS 'funcion que actualiza los estados despues del registro de un retroceso en el plan de pago';
COMMENT ON FUNCTION alm.f_generar_alta(p_id_usuario integer, p_id_usuario_ai integer, p_usuario varchar, p_id_preingreso integer)
IS 'Genera el ingreso a Almacén o a Activos Fijos a partir de un preingreso';
COMMENT ON FUNCTION alm.f_generar_alertas_mov(p_id_usuario integer, p_id_movimiento integer)
IS 'Generación de alertas en función de la definición de alarmas por item y almacén';
COMMENT ON FUNCTION alm.f_fun_regreso_movimiento_wf(p_id_usuario integer, p_id_usuario_ai integer, p_usuario_ai varchar, p_id_estado_wf integer, p_id_proceso_wf integer, p_codigo_estado varchar)
IS 'funcion que actualiza los estados despues del registro de un retroceso en el plan de pago';
COMMENT ON FUNCTION alm.f_generar_alta_old(p_id_usuario integer, p_id_usuario_ai integer, p_usuario varchar, p_id_preingreso integer)
IS 'Genera el ingreso a Almacén o a Activos Fijos a partir de un preingreso';
COMMENT ON FUNCTION alm.f_generar_cbtes(p_id_usuario integer, p_codigo_plantilla varchar, p_id_movimiento integer, p_parametros public.hstore)
IS 'Generación de comprobantes de almacén a partir de una Plantilla de Comprobante,
y empleando el generador de Comprobantes de PXP';
COMMENT ON FUNCTION alm.f_generar_ingreso(p_id_usuario integer, p_id_usuario_ai integer, p_usuario varchar, p_id_preingreso integer)
IS 'Genera el ingreso a Almacén o a Activos Fijos a partir de un preingreso';
COMMENT ON FUNCTION alm.f_generar_ingreso_old(p_id_usuario integer, p_id_usuario_ai integer, p_usuario varchar, p_id_preingreso integer)
IS 'Genera el ingreso a Almacén o a Activos Fijos a partir de un preingreso';
COMMENT ON FUNCTION alm.f_generar_mov_gestion(p_id_usuario integer, p_id_almacen_gestion_log integer, p_accion varchar)
IS 'Genera el(los) ingreso(s) o salida(s) para la acción requerida por gestión (apertura, cierre)';
COMMENT ON FUNCTION alm.f_generar_salida_func(p_id_usuario integer, p_id_salida_grupo integer)
IS 'Función que genera salidas para cada funcionario a partir de una salida grupal';
COMMENT ON FUNCTION alm.f_get_codigo_clasificacion_rec(p_id_clasificacion integer, p_padres_hijos varchar)
IS 'Funcion que devuelve los codigos los items en una categoria dada un codigo padre';
COMMENT ON FUNCTION alm.f_get_ruta_clasificacion(p_id_clasificacion integer)
IS 'funcion que devuelve la ruta de una clasificacion desde un identificador de tclasificacion';
COMMENT ON FUNCTION alm.f_get_num_mov(v_id_almacen integer, v_id_movimiento_tipo integer, v_fecha_mov timestamp)
IS 'Funcion que devuelve el numero de un movimiento dado el identificador del almacen,
el tipo de movimeinto y la fecha del movimiento';
COMMENT ON FUNCTION alm.f_get_items_movimiento(p_id_movimiento integer)
IS 'Funcion que devuelve la descripcion y cantidad de items de un movimiento';
COMMENT ON FUNCTION alm.f_get_id_tipo_mov_por_codigo(p_codigo_tipo_mov varchar)
IS 'Función que devuelve el id_movimiento_tipo en función al código del tipo mov';
COMMENT ON FUNCTION alm.f_get_id_clasificaciones_varios(p_id_clasificacion varchar, p_padres_hijos varchar)
IS 'Funcion que devuelve el id_clasificacion dada el id_clasificacion de un padre o hijo';
COMMENT ON FUNCTION alm.f_get_id_clasificaciones(p_id_clasificacion integer, p_padres_hijos varchar)
IS 'Funcion que devuelve los codigos de alm.tclafisifcacion de codigos padre o hijo ';
COMMENT ON FUNCTION alm.f_get_existencias_item(p_id_item integer)
IS 'Función que devuelve los la cantidad restante (saldo) existente del item con ID: p_id_item
 RETORNA:			Devuelve el valor de la cantidad disponible para el item: p_id_item
 					devuleve -1 cuando el saldo es negativo y null cuando el item no existe.';
COMMENT ON FUNCTION alm.f_get_correlativo(v_id_almacen integer, v_id_movimiento_tipo integer, v_periodo varchar)
IS ' Funcion que devuelve el dato correle_sig de alm.talmacen_correlativo dado los datos
id_almacen
id_movimiento_tipo
periodo ';
COMMENT ON FUNCTION alm.f_get_valorado_item(p_id_item integer, p_criterio_valoracion numeric)
IS 'Función que devuelve el costo unitario para el item: p_id_item en base
					al parametro de criterio de valoración: p_criterio_valoracion que puede ser
                    FIFO, LIFO o Promedio.
                    Posibles Valores  de p_criterio_valoracion:
                    FIFO: 1, LIFO: 2, Promedio: 3

RETORNA:			Devuelve el valor del costo unitario correspondiente al item: p_id_item
					segun el criterio de valoracion: p_criterio_valoracion.';
COMMENT ON FUNCTION alm.f_get_valorado_item(p_id_item integer, p_id_almacen integer, p_valoracion varchar, p_cantidad_sol numeric, p_fecha_mov date, out r_costo_valorado numeric, out r_cantidad_valorada numeric, out r_id_movimiento_det_val_desc integer)
IS 'Función que devuelve el costo unitario para el item: p_id_item en base
al parametro de criterio de valoración: p_criterio_valoracion que puede ser
                Promedio Ponderado, PEPS y UEPS.
                Posibles Valores  de p_criterio_valoracion:
                PP, PEPS, UEPS';
COMMENT ON FUNCTION alm.f_get_solicitantes_movimiento_dotaciones(p_codigo_dotaciones varchar)
IS 'Funcion que devuelve los datos de los funcionarios involucrados en un movimiento
RETORNA:desc_funcionario1
		funcionario.lugar_nombre
        funcionario.oficina_nombre
        item';
COMMENT ON FUNCTION alm.f_get_saldo_valorado_item(p_id_item integer, p_id_almacen integer, p_fecha_hasta date)
IS 'Función que devuelve la cantidad valorada existente del item con ID: p_id_item
 RETORNA:		Devuelve el valor de la cantidad disponible para el item: p_id_item';
COMMENT ON FUNCTION alm.f_get_saldo_item_val(p_id_item integer, p_id_almacen varchar, p_fecha_hasta date)
IS 'Función que devuelve la cantidad valorada existente del item con ID: p_id_item
 RETORNA:		Devuelve el valor de la cantidad disponible para el item: p_id_item';
COMMENT ON FUNCTION alm.f_get_saldo_item(p_id_item integer, pa_id_almacen varchar, p_fecha_hasta date)
IS 'Función que devuelve el saldo a una fecha de un item de uno o varios almacenes';
COMMENT ON FUNCTION alm.f_get_saldo_fisico_item_v2(p_id_item integer, p_id_almacen integer, p_fecha_hasta date, p_incluir_pendientes varchar)
IS 'Función que devuelve la cantidad existente del item con ID: p_id_item
 RETORNA:		Devuelve el valor de la cantidad disponible para el item: p_id_item';
COMMENT ON FUNCTION alm.f_get_saldo_fisico_item(p_id_item integer, p_id_almacen integer, p_fecha_hasta date, p_incluir_pendientes varchar)
IS 'Función que devuelve la cantidad existente del item con ID: p_id_item
 RETORNA:		Devuelve el valor de la cantidad disponible para el item: p_id_item';
COMMENT ON FUNCTION alm.f_movimiento_workflow_principal(p_id_usuario integer, p_parametros public.hstore)
IS 'Función que se encarga de direccionar el worflow principal de los movimientos.';
COMMENT ON FUNCTION alm.f_lista_funcionario_jefe_superior_wf_sel(p_id_usuario integer, p_id_tipo_estado integer, p_fecha date, p_id_estado_wf integer, p_count boolean, p_limit integer, p_start integer, p_filtro varchar)
IS 'Funcion que lista los fucionarios jefes superiores del funcionario';
COMMENT ON FUNCTION alm.f_item_existencia_almacen_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Función que a partir de un id_item y una fecha lista los almacenes
 			y la cantidad donde hayan existencia del item solicitado';
COMMENT ON FUNCTION alm.f_insertar_saldo_valorado(p_fecha date)
IS 'Funcion que inserta el saldo valorado de un item';
COMMENT ON FUNCTION alm.f_insertar_saldo_fisico(p_fecha date)
IS 'Funcion que inserta el saldo fisico de un item';
COMMENT ON FUNCTION alm.f_insercion_movimiento_externo(p_id_usuario integer, p_id_movimiento_tipo integer, p_id_almacen integer, p_id_funcionario integer, p_id_proveedor integer, p_id_almacen_dest integer, p_fecha_mov date, p_descripcion varchar, p_observaciones varchar, p_id_movimiento_origen integer, p_id_gestion integer, p_id_depto_conta integer)
IS 'Funcion que inserta un movimiento';
COMMENT ON FUNCTION alm.f_insercion_movimiento(p_id_usuario integer, p_parametros public.hstore)
IS 'Función para insertar un movimiento (Se la independiza para poder llamarla desde otras funciones)';
COMMENT ON FUNCTION alm.ft_almacen_gestion_log_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla ''alm.talmacen_gestion_log''';
COMMENT ON FUNCTION alm.ft_almacen_gestion_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla ''alm.talmacen_gestion''';
COMMENT ON FUNCTION alm.f_verificar_registro(v_id_almacen integer, v_id_movimiento_tipo integer, v_periodo varchar)
IS 'Funcion que verifica la cantidad de almacenes correlativo de un tipo de movimiento y periodo';
COMMENT ON FUNCTION alm.f_verificar_existencias_item(p_id_movimiento integer, p_estado varchar, out po_errores varchar, out po_contador integer, out po_alertas varchar, out po_saldo_total numeric)
IS 'Funcion que verifica si hay las existencias suficientes de un movimiento';
COMMENT ON FUNCTION alm.f_valoracion_mov(p_id_usuario integer, p_id_movimiento integer)
IS 'Función que realiza la valoración de los movimientos siguiendo los Métodos PEPS, UEPS o Promedio Ponderado,
en función de como está parametrizado el item por almacén';
COMMENT ON FUNCTION alm.f_update_estado_clasificacion_recursivo(v_id_clasificacion integer, v_estado_nuevo varchar, p_id_usuario integer)
IS 'Funcion que actualiza el estado de la tabla alm.tclasificacion ';
COMMENT ON FUNCTION alm.f_tri_talmacen_gestion()
IS 'Funcion que verifica que al eliminar un registro no tenga movimientos en su gestión';
COMMENT ON FUNCTION alm.f_regularizar()
IS 'funcion que notifica la cantidad de un momiento detalle valorado ';
COMMENT ON FUNCTION alm.f_registrar_almacen_gestion_log(p_id_usuario integer, p_id_almacen_gestion integer, p_accion varchar)
IS 'Función que genera el registro del log de la gestión de almacén en función
de la acción determinada (abrir, cerrar)';
COMMENT ON FUNCTION alm.f_nombre_clasificaciones_recursivo(v_id_clasificacion integer)
IS 'Funcion que devuelve el nombre de una clasificacion dependiendo del codigo de la tabla alm.tclasificacion';
COMMENT ON FUNCTION alm.ft_clasificacion_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''alm.tclasificacion''  ';
COMMENT ON FUNCTION alm.ft_clasificacion_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla ''alm.tclasificacion''';
COMMENT ON FUNCTION alm.ft_almacen_usuario_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''alm.talmacen_usuario''';
COMMENT ON FUNCTION alm.ft_almacen_usuario_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla ''alm.talmacen_usuario''';
COMMENT ON FUNCTION alm.ft_almacen_stock_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''param.tdepto_usuario''';
COMMENT ON FUNCTION alm.ft_almacen_stock_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla ''alm.talmacen_stock''';
COMMENT ON FUNCTION alm.ft_almacen_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''alm.talmacen''';
COMMENT ON FUNCTION alm.ft_almacen_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla ''alm.talmacen''';
COMMENT ON FUNCTION alm.ft_almacen_gestion_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''alm.talmacen_gestion''';
COMMENT ON FUNCTION alm.ft_almacen_gestion_log_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''alm.talmacen_gestion_log''';
COMMENT ON FUNCTION alm.ft_item_reemplazo_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones) de la tabla ''alm.titem''';
COMMENT ON FUNCTION alm.ft_item_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS ' Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones) de la tabla ''alm.titem''';
COMMENT ON FUNCTION alm.ft_item_concepto_ingas_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''alm.titem_concepto_ingas''';
COMMENT ON FUNCTION alm.ft_item_concepto_ingas_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla ''alm.titem_concepto_ingas''';
COMMENT ON FUNCTION alm.ft_item_archivo_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''alm.titem_archivo''';
COMMENT ON FUNCTION alm.ft_item_archivo_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla ''alm.titem_archivo''';
COMMENT ON FUNCTION alm.ft_inventario_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''alm.tinventario''';
COMMENT ON FUNCTION alm.ft_inventario_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS ' Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla ''alm.tinventario''';
COMMENT ON FUNCTION alm.ft_inventario_det_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''alm.tinventario_det''';
COMMENT ON FUNCTION alm.ft_inventario_det_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla ''alm.tinventario_det''';
COMMENT ON FUNCTION alm.ft_movimiento_grupo_det_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''alm.tmovimiento_grupo_det''';
COMMENT ON FUNCTION alm.ft_movimiento_grupo_det_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla ''alm.tmovimiento_grupo_det''';
COMMENT ON FUNCTION alm.ft_movimiento_det_valorado_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''param.tdepto_usuario''';
COMMENT ON FUNCTION alm.ft_movimiento_det_valorado_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla ''alm.tmovimiento_det''';
COMMENT ON FUNCTION alm.ft_movimiento_det_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''param.tdepto_usuario''';
COMMENT ON FUNCTION alm.ft_movimiento_det_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla alm.tmovimiento_det';
COMMENT ON FUNCTION alm.ft_metodo_val_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''alm.tmovimiento''';
COMMENT ON FUNCTION alm.ft_metodo_val_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones) de la tabla ''alm.tmovimiento''';
COMMENT ON FUNCTION alm.ft_item_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''alm.titem''';
COMMENT ON FUNCTION alm.ft_item_reemplazo_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''alm.titem''';
COMMENT ON FUNCTION alm.ft_movimiento_tipo_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''alm.tmovimiento''';
COMMENT ON FUNCTION alm.ft_movimiento_tipo_item_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''alm.tmovimiento_tipo_item''';
COMMENT ON FUNCTION alm.ft_movimiento_tipo_item_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla ''alm.tmovimiento_tipo_item''	';
COMMENT ON FUNCTION alm.ft_movimiento_tipo_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones) de la tabla ''alm.tmovimiento''';
COMMENT ON FUNCTION alm.ft_movimiento_tipo_almacen_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''alm.tmovimiento_tipo_almacen''';
COMMENT ON FUNCTION alm.ft_movimiento_tipo_almacen_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla ''alm.tmovimiento_tipo_almacen''';
COMMENT ON FUNCTION alm.ft_movimiento_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''alm.tmovimiento''';
COMMENT ON FUNCTION alm.ft_movimiento_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones) de la tabla ''alm.tmovimiento''';
COMMENT ON FUNCTION alm.ft_movimiento_grupo_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''alm.tmovimiento_grupo''';
COMMENT ON FUNCTION alm.ft_movimiento_grupo_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla ''alm.tmovimiento_grupo''';
COMMENT ON FUNCTION alm.ft_reporte_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros para los reportes del sistema de almacenes';
COMMENT ON FUNCTION alm.ft_rep_kardex_item_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve el kardex de un item de uno o varios almacenes en un periodo de tiempo';
COMMENT ON FUNCTION alm.ft_preingreso_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''alm.tpreingreso''';
COMMENT ON FUNCTION alm.ft_preingreso_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla ''alm.tpreingreso''';
COMMENT ON FUNCTION alm.ft_preingreso_det_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''alm.tpreingreso_det''';
COMMENT ON FUNCTION alm.ft_preingreso_det_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla ''alm.tpreingreso_det''';
COMMENT ON FUNCTION alm.ft_periodo_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''alm.tperiodo''';
COMMENT ON FUNCTION alm.ft_periodo_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla ''alm.tperiodo''';
COMMENT ON FUNCTION alm.ft_movimiento_tipo_uo_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS ' Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''alm.tmovimiento_tipo_uo''';
COMMENT ON FUNCTION alm.ft_movimiento_tipo_uo_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla ''alm.tmovimiento_tipo_uo''';
COMMENT ON FUNCTION alm.ftmp_mig_comibol(p_id_movimiento integer)
IS 'Funcion que genera el detalle para el movimiento inicial COMIBOL';
COMMENT ON FUNCTION alm.ft_salida_grupo_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''alm.tsalida_grupo''';
COMMENT ON FUNCTION alm.ft_salida_grupo_item_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''alm.tsalida_grupo_item''';
COMMENT ON FUNCTION alm.ft_salida_grupo_item_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla ''alm.tsalida_grupo_item''';
COMMENT ON FUNCTION alm.ft_salida_grupo_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla ''alm.tsalida_grupo''';
COMMENT ON FUNCTION alm.ft_salida_grupo_fun_sel(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''alm.tsalida_grupo_fun''';
COMMENT ON FUNCTION alm.ft_salida_grupo_fun_ime(p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
IS 'Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla ''alm.tsalida_grupo_fun''';







/***********************************F-SCP-ALAN-ALM-0-29/11/2019*****************************************/