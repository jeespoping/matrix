use Matrix;

drop table socios_000001;
drop table socios_000002;
drop table socios_000003;
drop table socios_000004;
drop table socios_000005;
drop table socios_000006;
drop table socios_000007;
drop table socios_000008;
drop table socios_000009;


CREATE TABLE socios_000001 (
  Medico varchar(8) NOT NULL default '',
  Fecha_data date NOT NULL default '0000-00-00',

  socced varchar(80) NOT NULL default '',             -- Nro de Documento del socio
  soctid varchar(80) NOT NULL default '',             -- Tipo de Documento
  socreg varchar(80) NOT NULL default '',             -- Registro profesional  (Opcional)
  socap1 varchar(80) NOT NULL default '',             -- 1er Apellido
  socap2 varchar(80) NOT NULL default '',             -- 2do Apellido          (Opcional) 
  socnom varchar(80) NOT NULL default '',             -- Nombres
  socnac date NOT NULL default '0000-00-00',          -- Fecha de Nacimiento
  socida varchar(80) NOT NULL default '',             -- Idioma 1              (Opcional) 
  socidb varchar(80) NOT NULL default '',             -- Idioma 2              (Opcional)
  socidc varchar(80) NOT NULL default '',             -- Idioma 3              (Opcional) 
  socdes date NOT NULL default '0000-00-00',          -- Socio Desde
  soccoa varchar(80) NOT NULL default '',             -- Correo electronico 1  (Opcional)
  soccob varchar(80) NOT NULL default '',             -- Correo electronico 2  (Opcional)
  socdir varchar(80) NOT NULL default '',             -- Direccion
  socdep varchar(80) NOT NULL default '',             -- Departamento         ( Dpto es 01=Otro pais )
  socmun varchar(80) NOT NULL default '',             -- Ciudad o Municipio
  socapa varchar(80) NOT NULL default '',             -- Apartado Aereo         
  soctre varchar(80) NOT NULL default '',             -- Telefono Residencia   (Opcional)
  soctof varchar(80) NOT NULL default '',             -- Telefono Oficina      (Opcional)
  soctce varchar(80) NOT NULL default '',             -- Telefono Celular      (Opcional)
  soceci varchar(80) NOT NULL default '',             -- Estado Civil
  socsex varchar(80) NOT NULL default '',             -- Sexo

  socpol varchar(80) NOT NULL default '',             -- Poliza
  socmon double NOT NULL default '0',                 -- Monto
  socemp varchar(80) NOT NULL default '',             -- Empresa que expide
  socvig date NOT NULL default '0000-00-00',          -- Vigencia
  socaso varchar(80) NOT NULL default '',             -- Asociaciones Cientificas a las que pertenece  (Opcional)
  
  socnum varchar(80) NOT NULL default '',             -- Nro de Registro
  socfec date NOT NULL default '0000-00-00',          -- Fecha del titulo
  soctic int NOT null,                                -- Titulos cancelados
  socpri int NOT null,                                -- Nro de acciones privilegiadas 
  socint double NOT NULL default '0',                 -- Intereses pagados privilegiados
  socacc int NOT null,                                -- Nro de acciones ordinarias
  socdiv date NOT NULL default '0000-00-00',          -- Ultimo dividendo pagado    
  socobs varchar(80) NOT NULL default '',             -- Descripcion u observaciones
  socact varchar(80) NOT NULL default '',             -- Estado A=Activo I=Inactivo 

  Seguridad varchar(10) NOT NULL default '',
  id bigint(20) NOT NULL auto_increment,
  PRIMARY KEY  (id),
  UNIQUE KEY socios_000001_idx (socced)               
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

  create index socios_000001_idx1 ON socios_000001 (socreg);
  create index socios_000001_idx2 ON socios_000001 (socdes);


CREATE TABLE socios_000002 (
  Medico varchar(8) NOT NULL default '',
  Fecha_data date NOT NULL default '0000-00-00',
 
  famced varchar(80) NOT NULL default '',             -- Nro de Documento del socio
  famdoc varchar(80) NOT NULL default '',             -- Nro de Documento del familiar
  famtid varchar(80) NOT NULL default '',             -- Tipo de Documento del familiar
  famtip varchar(80) NOT NULL default '',             -- Tipo de parentesco
  famap1 varchar(80) NOT NULL default '',             -- 1er Apellido
  famap2 varchar(80) NOT NULL default '',             -- 2do Apellido          (Opcional) 
  famnom varchar(80) NOT NULL default '',             -- Nombres
  famnac date NOT NULL default '0000-00-00',          -- Facha de nacimiento

  Seguridad varchar(10) NOT NULL default '',
  id bigint(20) NOT NULL auto_increment,
  PRIMARY KEY  (id),
  UNIQUE KEY socios_000002_idx (famced,famdoc)               
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

  create index socios_000002_idx1 ON socios_000002 (famtip);

CREATE TABLE socios_000003 (
  Medico varchar(8) NOT NULL default '',
  Fecha_data date NOT NULL default '0000-00-00',
  
  uniced varchar(80) NOT NULL default '',            -- Nro de Documento del socio
  unipro varchar(80) NOT NULL default '',            -- Profesion
  uniuni varchar(80) NOT NULL default '',            -- Universidad
  unireg varchar(80) NOT NULL default '',            -- Registro
  uniact varchar(80) NOT NULL default '',            -- Acta de grado
  unimun varchar(80) NOT NULL default '',            -- Ciudad o Municipio
  unidep varchar(80) NOT NULL default '',            -- Departamento
  unipai varchar(80) NOT NULL default '',            -- Pais                 (Si Dpto es 01=Otro pais  este campo sera obligatorio)
  unihom varchar(80) NOT NULL default '',            -- Homologacion
 
  Seguridad varchar(10) NOT NULL default '',
  id bigint(20) NOT NULL auto_increment,
  PRIMARY KEY  (id),
  UNIQUE KEY socios_000003_idx (uniced,unipro)               
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
 
  create index socios_000003_idx1 ON socios_000003 (unipro);
  create index socios_000003_idx2 ON socios_000003 (uniuni);
 
CREATE TABLE socios_000004 (
  Medico varchar(8) NOT NULL default '',
  Fecha_data date NOT NULL default '0000-00-00',
  
  espced varchar(80) NOT NULL default '',            -- Nro de Documento del socio
  espesp varchar(80) NOT NULL default '',            -- Especialidad 
  esptip varchar(80) NOT NULL default '',            -- Tipo 1:Especialidad 2:Subespecialidad

  Seguridad varchar(10) NOT NULL default '',
  id bigint(20) NOT NULL auto_increment,
  PRIMARY KEY  (id),
  UNIQUE KEY socios_000004_idx (espced,espesp)               
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

  create index socios_000004_idx1 ON socios_000004 (esptip);

CREATE TABLE socios_000005 (
  Medico varchar(8) NOT NULL default '',
  Fecha_data date NOT NULL default '0000-00-00',

  empced varchar(80) NOT NULL default '',            -- Nro de Documento del socio
  empemp varchar(80) NOT NULL default '',            -- Empresa de PMLA 
  emptvi varchar(80) NOT NULL default '',            -- Tipo de Vinculacion  
  empcar varchar(80) NOT NULL default '',            -- cargo
  empcoo varchar(80) NOT NULL default '',            -- Coordinador

  Seguridad varchar(10) NOT NULL default '',
  id bigint(20) NOT NULL auto_increment,
  PRIMARY KEY  (id),
  UNIQUE KEY socios_000005_idx (empced,empemp)               
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

  create index socios_000005_idx1 ON socios_000005 (emptvi);

CREATE TABLE socios_000006 (
  Medico varchar(8) NOT NULL default '',
  Fecha_data date NOT NULL default '0000-00-00',

  traced varchar(80) NOT NULL default '',            -- Nro de Documento del socio
  tratit varchar(80) NOT NULL default '',            -- Nro del titulo o documento
  tratra varchar(80) NOT NULL default '',            -- Transaccion
  tranro varchar(80) NOT NULL default '',            -- Nro de acciones a transar 
  tratia varchar(80) NOT NULL default '',            -- Tipo accion     
           
  Seguridad varchar(10) NOT NULL default '',
  id bigint(20) NOT NULL auto_increment,
  PRIMARY KEY  (id),
  UNIQUE KEY socios_000006_idx (traced,tratit)               
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

  create index socios_000006_idx1 ON socios_000006 (tratra);

CREATE TABLE socios_000007 (
  Medico varchar(8) NOT NULL default '',
  Fecha_data date NOT NULL default '0000-00-00',
  
  prfcod varchar(80) NOT NULL default '',            -- Codigo de la profesion
  prfdes varchar(80) NOT NULL default '',            -- Descripcion de la profesion
  prfest varchar(80) NOT NULL default '',            -- Estado

  Seguridad varchar(10) NOT NULL default '',
  id bigint(20) NOT NULL auto_increment,
  PRIMARY KEY  (id),
  UNIQUE KEY socios_000007_idx (prfcod)               
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE socios_000008 (
  Medico varchar(8) NOT NULL default '',
  Fecha_data date NOT NULL default '0000-00-00',
  
  unicod varchar(80) NOT NULL default '',            -- Codigo de la universidad
  unides varchar(80) NOT NULL default '',            -- Descripcion de la univesidad
  uniest varchar(80) NOT NULL default '',            -- Estado

  Seguridad varchar(10) NOT NULL default '',
  id bigint(20) NOT NULL auto_increment,
  PRIMARY KEY  (id),
  UNIQUE KEY socios_000008_idx (unicod)               
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE socios_000009 (
  Medico varchar(8) NOT NULL default '',
  Fecha_data date NOT NULL default '0000-00-00',
  
  tipcod varchar(80) NOT NULL default '',            -- Codigo del tipo de movimiento
  tipdes varchar(80) NOT NULL default '',            -- Descripcion del movimiento
  tipest varchar(80) NOT NULL default '',            -- Estado

  Seguridad varchar(10) NOT NULL default '',
  id bigint(20) NOT NULL auto_increment,
  PRIMARY KEY  (id),
  UNIQUE KEY socios_000009_idx (tipcod)               
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
        

show databases;
--show tables;
describe socios_000001;


    







