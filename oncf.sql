
/*==============================================================*/
/* Table : HISTORIQUE                                           */
/*==============================================================*/
create table HISTORIQUE 
(
   ID_HISTORIQUE        integer                        not null,
   USERNAME             varchar(256)                   not null,
   NUM_WAGON            integer                        not null,
   ACTION               long varchar                   not null,
   DETAILS              varchar(256)                   not null,
   DATE_ACTION          date                           not null,
   constraint PK_HISTORIQUE primary key (ID_HISTORIQUE)
);

/*==============================================================*/
/* Table : UTILISATEUR                                          */
/*==============================================================*/
create table UTILISATEUR 
(
   USERNAME             varchar(256)                   not null,
   MDP                  varchar(256)                   not null,
   NOM                  long varchar                   not null,
   ROLE                 long varchar                   not null,
   constraint PK_UTILISATEUR primary key (USERNAME)
);

/*==============================================================*/
/* Table : WAGON                                                */
/*==============================================================*/
create table WAGON 
(
   NUM_WAGON            integer                        not null,
   SERIE                long varchar                   not null,
   DERNIER_DATE_VG      date                           not null,
   DERNIER_DATE_RL      date                           not null,
   constraint PK_WAGON primary key (NUM_WAGON)
);

alter table HISTORIQUE
   add constraint FK_HISTORIQ_CREE_UTILISAT foreign key (USERNAME)
      references UTILISATEUR (USERNAME)
      on update restrict
      on delete restrict;

alter table HISTORIQUE
   add constraint FK_HISTORIQ_POSSEDE_WAGON foreign key (NUM_WAGON)
      references WAGON (NUM_WAGON)
      on update restrict
      on delete restrict;

insert into wagon (num_wagon, dernier_date_RL, dernier_date_VG, serie)
values (692001,'2020-03-30','2024-05-13','Taoos phosphate'),
	(692002,'2020-01-30','2024-02-19','Taoos phosphate'),
	(692003,'2020-09-21','2023-11-13','Taoos phosphate'),
	(692004,'2020-06-27','2024-08-31','Taoos phosphate'),
	(692005,'2020-01-14','2024-02-06','Taoos phosphate'),
	(692006,'2021-07-10','2024-10-09','Taoos phosphate'),
	(692007,'2020-02-19','2024-02-14','Taoos phosphate'),
	(692008,'2020-08-28','2024-08-29','Taoos phosphate'),
	(692009,'2020-03-31','2024-04-05','Taoos phosphate'),
	(692010,'2020-04-14','2024-03-12','Taoos phosphate'),
	(692011,'2020-10-17','2024-02-01','Taoos phosphate'),
	(692012,'2020-02-29','2024-01-10','Taoos phosphate'),
	(692013,'2022-07-23','2024-08-12','Taoos phosphate'),
	(692014,'2020-02-28','2024-04-03','Taoos phosphate'),
	(692015,'2020-03-27','2024-05-02','Taoos phosphate'),
	(692016,'2020-07-08','2024-08-02','Taoos phosphate'),
	(692017,'2023-07-21','2024-08-15','Taoos phosphate'),
	(692018,'2020-02-08','2024-04-12','Taoos phosphate'),
	(692019,'2020-06-17','2024-07-05','Taoos phosphate');
    select * from wagon;
insert into utilisateur (USERNAME, MDP, NOM, ROLE) values ('brahim06', 'flyou9133', 'ibrahim', 'admin');
ALTER TABLE historique MODIFY ID_HISTORIQUE INT AUTO_INCREMENT;
ALTER TABLE historique modify DATE_ACTION DATETIME DEFAULT CURRENT_TIMESTAMP;
-- Recréer la contrainte avec CASCADE
ALTER TABLE historique 
ADD CONSTRAINT FK_HISTORIQ_POSSEDE_WAGON 
FOREIGN KEY (NUM_WAGON) REFERENCES wagon(NUM_WAGON) 
;
ALTER TABLE wagon ADD EST_SUPPRIME TINYINT(1) DEFAULT 0;
