SET NAMES utf8;

CREATE TABLE clients (
  id int(11) NOT NULL AUTO_INCREMENT,
  client_name varchar(255),
  sync_dir varchar(255),
  remote_sync_account varchar(255),
  remote_sync_dir varchar(255),
  status varchar(50) DEFAULT 'active',
   create_date datetime,
  update_date datetime,
 PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into clients (id, client_name, sync_dir, remote_sync_account, remote_sync_dir, status, create_date, update_date) values(1, 'xxxxxx', 'xxxxxx',"xxxxxx@192.168.0.1","/var/xxx/www/stg.xxx.jp/current/cakephp/app/tmp/logs/stepmail", 'active', now(), now());
insert into clients (id, client_name, sync_dir, remote_sync_account, remote_sync_dir, status, create_date, update_date) values(2, 'xxxxxx', 'xxxxxx',"xxxxxx@192.168.0.1","/home/xxx/xxx/current/log/stepmail", 'active', now(), now());





CREATE TABLE stepmail_settings (
  id int(11) NOT NULL AUTO_INCREMENT,
  client_id int(11) NOT NULL,
  mailtype_title varchar(255) ,
  status varchar(50) DEFAULT 'active',
  list_name varchar(255),
  list_charset varchar(255),
  conts_name varchar(255),
  create_date datetime,
  update_date datetime,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into stepmail_settings (client_id,mailtype_title, list_name, conts_name, create_date, update_date, list_charset) values(1, '無料トライアル申し込みの3日後', 'mail-step1_YYYYMMDD.list', 'mail-step1.conts', now(), now(), 'UTF-8');
insert into stepmail_settings (client_id,mailtype_title, list_name, conts_name, create_date, update_date, list_charset) values(1, '無料トライアル申し込みの7日後', 'mail-step2_YYYYMMDD.list', 'mail-step2.conts', now(), now(), 'UTF-8');
insert into stepmail_settings (client_id,mailtype_title, list_name, conts_name, create_date, update_date, list_charset) values(1, '無料トライアル申し込みの10日後(終了3日前）', 'mail-step3_YYYYMMDD.list', 'mail-step3.conts', now(), now(), 'UTF-8');
insert into stepmail_settings (client_id,mailtype_title, list_name, conts_name, create_date, update_date,status, list_charset) values(1, '無料トライアル申し込みの10日後(終了3日前）', 'mail-step4_YYYYMMDD.list', 'mail-step4.conts', now(), now(), 'inactive', 'UTF-8');
insert into stepmail_settings (client_id,mailtype_title, list_name, conts_name, create_date, update_date,status, list_charset) values(2, 'ご利用状況お伺いメール2日後', 'xxxxxx-step1.YYYYMMDD.list', 'xxxxxx-step1.conts', now(), now(), 'active', 'UTF-8');
insert into stepmail_settings (client_id,mailtype_title, list_name, conts_name, create_date, update_date,status, list_charset) values(2, '制作パートナーのご案内メール3日後', 'xxxxxx-step2.YYYYMMDD.list', 'xxxxxx-step2.conts', now(), now(), 'active', 'UTF-8');


CREATE TABLE delivery_logs (
  id int(11) NOT NULL AUTO_INCREMENT,
  stepmail_setting_id int(11) NOT NULL,
  task_id varchar(100) ,
  process_date datetime,
  results text,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE delivery_logs ADD COLUMN sendlist_data longtext;

CREATE TABLE convert_listdata_settings (
  id int(11) NOT NULL AUTO_INCREMENT,
  stepmail_setting_id int(11) NOT NULL,
  show_column varchar(255) ,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into convert_listdata_settings (stepmail_setting_id, show_column) values(1,'Company');
insert into convert_listdata_settings (stepmail_setting_id, show_column) values(1,'Division');
insert into convert_listdata_settings (stepmail_setting_id, show_column) values(1,'Name');
insert into convert_listdata_settings (stepmail_setting_id, show_column) values(1,'OwnerId');
insert into convert_listdata_settings (stepmail_setting_id, show_column) values(2,'Company');
insert into convert_listdata_settings (stepmail_setting_id, show_column) values(2,'Division');
insert into convert_listdata_settings (stepmail_setting_id, show_column) values(2,'Name');
insert into convert_listdata_settings (stepmail_setting_id, show_column) values(2,'OwnerId');
insert into convert_listdata_settings (stepmail_setting_id, show_column) values(3,'Company');
insert into convert_listdata_settings (stepmail_setting_id, show_column) values(3,'Division');
insert into convert_listdata_settings (stepmail_setting_id, show_column) values(3,'Name');
insert into convert_listdata_settings (stepmail_setting_id, show_column) values(3,'OwnerId');
insert into convert_listdata_settings (stepmail_setting_id, show_column) values(5,'USERNAME');
insert into convert_listdata_settings (stepmail_setting_id, show_column) values(6,'USERNAME');


CREATE TABLE batch_manager (
  id int(11) NOT NULL AUTO_INCREMENT,
  client_id int(11) ,
  stepmail_setting_id int(11) ,
  batch_class varchar(255) ,
  month int(11) ,
  day int(11),
  week int(11),
  hour int(11) ,
  minute  int(11) ,
  proccess_flg varchar(255),
  process_startdate datetime,
  last_enddate datetime,
  create_date datetime,
  update_date datetime,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into batch_manager (client_id, stepmail_setting_id, batch_class, hour, minute, create_date, update_date) values(null, 1, 'StepmailBatch', 10,0, now(), now());
insert into batch_manager (client_id, stepmail_setting_id, batch_class,hour, minute, create_date, update_date) values(null, 2, 'StepmailBatch',  10,0, now(), now());
insert into batch_manager (client_id, stepmail_setting_id, batch_class,hour, minute, create_date, update_date) values(null, 3, 'StepmailBatch',  10,0, now(), now());
insert into batch_manager (client_id, stepmail_setting_id, batch_class,hour, minute, create_date, update_date) values(null, 4, 'StepmailBatch',  10,0, now(), now());
insert into batch_manager (client_id, stepmail_setting_id, batch_class,hour, minute, create_date, update_date) values(null, 5, 'StepmailBatch',  10,30, now(), now());
insert into batch_manager (client_id, stepmail_setting_id, batch_class,hour, minute, create_date, update_date) values(null, 6, 'StepmailBatch',  10,30, now(), now());
insert into batch_manager (client_id, stepmail_setting_id, batch_class,hour, minute, create_date, update_date) values(null, null, 'MonitoringErrorBatch',  null,30, now(), now());
insert into batch_manager (client_id, stepmail_setting_id, batch_class,hour, minute, create_date, update_date) values(null, null, 'SyncFromAPP',  null,null, now(), now());
insert into batch_manager (client_id, stepmail_setting_id, batch_class,hour, minute, create_date, update_date) values(null, null, 'ReportDeliveryLogs',  13,0, now(), now());


update stepmail_settings set mailtype_title = '利用状況確認状況3日後' where id = 5;
update stepmail_settings set mailtype_title = 'xxxxxx変換にお困りの方へ5日後' where id = 6;

update stepmail_settings set mailtype_title = '利用状況確認状況2日後' where id = 5;
update stepmail_settings set mailtype_title = 'xxxxxx変換にお困りの方へ4日後' where id = 6;
