--
-- PostgreSQL database dump
--

-- Dumped from database version 9.5.19
-- Dumped by pg_dump version 9.5.19

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: site2; Type: DATABASE; Schema: -; Owner: paas
--

CREATE DATABASE site2 WITH TEMPLATE = template0 ENCODING = 'UTF8';


ALTER DATABASE site2 OWNER TO paas;

\connect site2

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: f11; Type: TABLE; Schema: public; Owner: paas
--

CREATE TABLE public.f11 (
    upload_at timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL,
    machine_detail json NOT NULL
);


ALTER TABLE public.f11 OWNER TO paas;

--
-- Name: TABLE f11; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON TABLE public.f11 IS 'f11';


--
-- Name: COLUMN f11.upload_at; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.f11.upload_at IS '上傳時間';


--
-- Name: f12; Type: TABLE; Schema: public; Owner: paas
--

CREATE TABLE public.f12 (
    upload_at timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL,
    machine_detail json
);


ALTER TABLE public.f12 OWNER TO paas;

--
-- Name: f123; Type: TABLE; Schema: public; Owner: paas
--

CREATE TABLE public.f123 (
    upload_at timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL,
    machine_detail json
);


ALTER TABLE public.f123 OWNER TO paas;

--
-- Name: TABLE f123; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON TABLE public.f123 IS 'f123';


--
-- Name: COLUMN f123.upload_at; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.f123.upload_at IS '上傳時間';


--
-- Name: COLUMN f123.machine_detail; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.f123.machine_detail IS '機台詳細資料';


--
-- Name: inspection; Type: TABLE; Schema: public; Owner: paas
--

CREATE TABLE public.inspection (
    upload_at timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL,
    work_code character varying(50),
    project_id integer,
    inspection_detail json,
    result integer,
    creator integer
);


ALTER TABLE public.inspection OWNER TO paas;

--
-- Name: TABLE inspection; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON TABLE public.inspection IS '檢測紀錄資料表';


--
-- Name: COLUMN inspection.upload_at; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.inspection.upload_at IS '上傳時間';


--
-- Name: COLUMN inspection.work_code; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.inspection.work_code IS '派工單號';


--
-- Name: COLUMN inspection.project_id; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.inspection.project_id IS '製程代號';


--
-- Name: COLUMN inspection.inspection_detail; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.inspection.inspection_detail IS '檢測數據';


--
-- Name: COLUMN inspection.result; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.inspection.result IS '檢測結果(0=不合格,1= 合格)';


--
-- Name: COLUMN inspection.creator; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.inspection.creator IS '建立者';


--
-- Name: machine_alarm_video; Type: TABLE; Schema: public; Owner: paas
--

CREATE TABLE public.machine_alarm_video (
    upload_at timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL,
    device_name character varying(50),
    alarm_time timestamp without time zone,
    video_name character varying(256)
);


ALTER TABLE public.machine_alarm_video OWNER TO paas;

--
-- Name: TABLE machine_alarm_video; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON TABLE public.machine_alarm_video IS '機台警報影片名稱資料表';


--
-- Name: COLUMN machine_alarm_video.upload_at; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.machine_alarm_video.upload_at IS '上傳時間';


--
-- Name: COLUMN machine_alarm_video.device_name; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.machine_alarm_video.device_name IS '機台編號';


--
-- Name: COLUMN machine_alarm_video.alarm_time; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.machine_alarm_video.alarm_time IS '警報時間';


--
-- Name: COLUMN machine_alarm_video.video_name; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.machine_alarm_video.video_name IS '影片名稱';


--
-- Name: machine_on_off_hist; Type: TABLE; Schema: public; Owner: paas
--

CREATE TABLE public.machine_on_off_hist (
    upload_at timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL,
    device_name character varying(50) NOT NULL,
    comp_id character varying(128) NOT NULL,
    site_id character varying(128) NOT NULL,
    status character varying(1) NOT NULL
);


ALTER TABLE public.machine_on_off_hist OWNER TO paas;

--
-- Name: TABLE machine_on_off_hist; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON TABLE public.machine_on_off_hist IS '機台開機狀態歷史資料表';


--
-- Name: COLUMN machine_on_off_hist.upload_at; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.machine_on_off_hist.upload_at IS '上傳時間';


--
-- Name: COLUMN machine_on_off_hist.device_name; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.machine_on_off_hist.device_name IS '設備名稱';


--
-- Name: COLUMN machine_on_off_hist.comp_id; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.machine_on_off_hist.comp_id IS '公司編號';


--
-- Name: COLUMN machine_on_off_hist.site_id; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.machine_on_off_hist.site_id IS '工廠編號';


--
-- Name: COLUMN machine_on_off_hist.status; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.machine_on_off_hist.status IS '狀態(S=開始,E=結束)';


--
-- Name: machine_status_sum; Type: TABLE; Schema: public; Owner: paas
--

CREATE TABLE public.machine_status_sum (
    upload_at timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL,
    device_name character varying(50) NOT NULL,
    machine_detail json
);


ALTER TABLE public.machine_status_sum OWNER TO paas;

--
-- Name: TABLE machine_status_sum; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON TABLE public.machine_status_sum IS '機台狀態摘要表';


--
-- Name: COLUMN machine_status_sum.upload_at; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.machine_status_sum.upload_at IS '上傳時間';


--
-- Name: COLUMN machine_status_sum.device_name; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.machine_status_sum.device_name IS '機台名稱';


--
-- Name: COLUMN machine_status_sum.machine_detail; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.machine_status_sum.machine_detail IS '機台詳細資料';


--
-- Name: material_batch_no_abn; Type: TABLE; Schema: public; Owner: paas
--

CREATE TABLE public.material_batch_no_abn (
    upload_at timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL,
    materia_code character varying(50),
    material_batch_no character varying(50),
    work_code character varying(50),
    material_err_code character varying(20),
    creator integer
);


ALTER TABLE public.material_batch_no_abn OWNER TO paas;

--
-- Name: TABLE material_batch_no_abn; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON TABLE public.material_batch_no_abn IS '耗材異常紀錄資料表';


--
-- Name: COLUMN material_batch_no_abn.upload_at; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.material_batch_no_abn.upload_at IS '上傳時間';


--
-- Name: COLUMN material_batch_no_abn.materia_code; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.material_batch_no_abn.materia_code IS '耗材編號';


--
-- Name: COLUMN material_batch_no_abn.material_batch_no; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.material_batch_no_abn.material_batch_no IS '耗材批號';


--
-- Name: COLUMN material_batch_no_abn.work_code; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.material_batch_no_abn.work_code IS '派工單號';


--
-- Name: COLUMN material_batch_no_abn.material_err_code; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.material_batch_no_abn.material_err_code IS '耗材異常編號';


--
-- Name: COLUMN material_batch_no_abn.creator; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.material_batch_no_abn.creator IS '建立者';


--
-- Name: material_batch_no_use; Type: TABLE; Schema: public; Owner: paas
--

CREATE TABLE public.material_batch_no_use (
    upload_at timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL,
    materia_code character varying(50),
    material_batch_no character varying(50),
    work_code character varying(50),
    status character varying(1),
    cnt integer,
    creator integer
);


ALTER TABLE public.material_batch_no_use OWNER TO paas;

--
-- Name: TABLE material_batch_no_use; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON TABLE public.material_batch_no_use IS '耗材上/下線紀錄資料表';


--
-- Name: COLUMN material_batch_no_use.upload_at; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.material_batch_no_use.upload_at IS '上傳時間';


--
-- Name: COLUMN material_batch_no_use.materia_code; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.material_batch_no_use.materia_code IS '耗材編號';


--
-- Name: COLUMN material_batch_no_use.material_batch_no; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.material_batch_no_use.material_batch_no IS '耗材批號';


--
-- Name: COLUMN material_batch_no_use.work_code; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.material_batch_no_use.work_code IS '派工單號';


--
-- Name: COLUMN material_batch_no_use.status; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.material_batch_no_use.status IS '狀態(S=開始,E=結束)';


--
-- Name: COLUMN material_batch_no_use.cnt; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.material_batch_no_use.cnt IS '支數';


--
-- Name: COLUMN material_batch_no_use.creator; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.material_batch_no_use.creator IS '建立者';


--
-- Name: mould_series_no_abn; Type: TABLE; Schema: public; Owner: paas
--

CREATE TABLE public.mould_series_no_abn (
    upload_at timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL,
    mould_code character varying(50),
    mould_series_no character varying(50),
    work_code character varying(50),
    mould_err_code character varying(20),
    creator integer
);


ALTER TABLE public.mould_series_no_abn OWNER TO paas;

--
-- Name: TABLE mould_series_no_abn; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON TABLE public.mould_series_no_abn IS '小模異常紀錄資料表';


--
-- Name: COLUMN mould_series_no_abn.upload_at; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.mould_series_no_abn.upload_at IS '上傳時間';


--
-- Name: COLUMN mould_series_no_abn.mould_code; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.mould_series_no_abn.mould_code IS '小模編號';


--
-- Name: COLUMN mould_series_no_abn.mould_series_no; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.mould_series_no_abn.mould_series_no IS '小模序號';


--
-- Name: COLUMN mould_series_no_abn.work_code; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.mould_series_no_abn.work_code IS '派工單號';


--
-- Name: COLUMN mould_series_no_abn.mould_err_code; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.mould_series_no_abn.mould_err_code IS '小模異常編號';


--
-- Name: COLUMN mould_series_no_abn.creator; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.mould_series_no_abn.creator IS '建立者';


--
-- Name: mould_series_no_use; Type: TABLE; Schema: public; Owner: paas
--

CREATE TABLE public.mould_series_no_use (
    upload_at timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL,
    mould_code character varying(50),
    mould_series_no character varying(50),
    work_code character varying(50),
    status character varying(1),
    cnt integer,
    creator integer
);


ALTER TABLE public.mould_series_no_use OWNER TO paas;

--
-- Name: TABLE mould_series_no_use; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON TABLE public.mould_series_no_use IS '小模上/下線紀錄資料表';


--
-- Name: COLUMN mould_series_no_use.upload_at; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.mould_series_no_use.upload_at IS '上傳時間';


--
-- Name: COLUMN mould_series_no_use.mould_code; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.mould_series_no_use.mould_code IS '小模編號';


--
-- Name: COLUMN mould_series_no_use.mould_series_no; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.mould_series_no_use.mould_series_no IS '小模序號';


--
-- Name: COLUMN mould_series_no_use.work_code; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.mould_series_no_use.work_code IS '派工單號';


--
-- Name: COLUMN mould_series_no_use.status; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.mould_series_no_use.status IS '狀態(S=開始,E=結束)';


--
-- Name: COLUMN mould_series_no_use.cnt; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.mould_series_no_use.cnt IS '支數';


--
-- Name: COLUMN mould_series_no_use.creator; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.mould_series_no_use.creator IS '建立者';


--
-- Name: r09; Type: TABLE; Schema: public; Owner: paas
--

CREATE TABLE public.r09 (
    upload_at timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL,
    machine_detail json
);


ALTER TABLE public.r09 OWNER TO paas;

--
-- Name: TABLE r09; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON TABLE public.r09 IS '機台r12';


--
-- Name: COLUMN r09.upload_at; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.r09.upload_at IS '上傳時間';


--
-- Name: COLUMN r09.machine_detail; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.r09.machine_detail IS '機台資料';


--
-- Name: runcard; Type: TABLE; Schema: public; Owner: paas
--

CREATE TABLE public.runcard (
    upload_at timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL,
    runcard_code character varying(40),
    project_id integer,
    furnace_code character varying(50),
    identify integer,
    screw_weight numeric(18,5),
    creator integer
);


ALTER TABLE public.runcard OWNER TO paas;

--
-- Name: TABLE runcard; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON TABLE public.runcard IS '流程卡紀錄資料表';


--
-- Name: COLUMN runcard.upload_at; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.runcard.upload_at IS '上傳時間';


--
-- Name: COLUMN runcard.runcard_code; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.runcard.runcard_code IS '流程卡號';


--
-- Name: COLUMN runcard.project_id; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.runcard.project_id IS '製程代號';


--
-- Name: COLUMN runcard.furnace_code; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.runcard.furnace_code IS '線材爐號';


--
-- Name: COLUMN runcard.identify; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.runcard.identify IS '識別(0=合格,1=不合格)';


--
-- Name: COLUMN runcard.screw_weight; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.runcard.screw_weight IS '生產工件重量';


--
-- Name: COLUMN runcard.creator; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.runcard.creator IS '建立者';


--
-- Name: runcard_thread_hist; Type: TABLE; Schema: public; Owner: paas
--

CREATE TABLE public.runcard_thread_hist (
    upload_at timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL,
    runcard_code character varying(40) NOT NULL,
    device_name character varying(50),
    creator integer
);


ALTER TABLE public.runcard_thread_hist OWNER TO paas;

--
-- Name: TABLE runcard_thread_hist; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON TABLE public.runcard_thread_hist IS '輾牙機台流程卡紀錄表';


--
-- Name: COLUMN runcard_thread_hist.upload_at; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.runcard_thread_hist.upload_at IS '上傳時間';


--
-- Name: COLUMN runcard_thread_hist.runcard_code; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.runcard_thread_hist.runcard_code IS '流程卡號';


--
-- Name: COLUMN runcard_thread_hist.device_name; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.runcard_thread_hist.device_name IS '設備名稱(R02、R05)';


--
-- Name: COLUMN runcard_thread_hist.creator; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.runcard_thread_hist.creator IS '建立者';


--
-- Name: test; Type: TABLE; Schema: public; Owner: paas
--

CREATE TABLE public.test (
    seq bigint NOT NULL,
    machine_detail json NOT NULL
);


ALTER TABLE public.test OWNER TO paas;

--
-- Name: TABLE test; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON TABLE public.test IS 'test';


--
-- Name: COLUMN test.seq; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.test.seq IS 'SEQ';


--
-- Name: COLUMN test.machine_detail; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.test.machine_detail IS '測試JSON';


--
-- Name: test_seq_seq; Type: SEQUENCE; Schema: public; Owner: paas
--

CREATE SEQUENCE public.test_seq_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.test_seq_seq OWNER TO paas;

--
-- Name: test_seq_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: paas
--

ALTER SEQUENCE public.test_seq_seq OWNED BY public.test.seq;


--
-- Name: thread_series_no_abn; Type: TABLE; Schema: public; Owner: paas
--

CREATE TABLE public.thread_series_no_abn (
    upload_at timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL,
    thread_code character varying(50),
    thread_series_no character varying(50),
    work_code character varying(50),
    thread_err_code character varying(20),
    creator integer
);


ALTER TABLE public.thread_series_no_abn OWNER TO paas;

--
-- Name: TABLE thread_series_no_abn; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON TABLE public.thread_series_no_abn IS '牙板異常紀錄資料表';


--
-- Name: COLUMN thread_series_no_abn.upload_at; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.thread_series_no_abn.upload_at IS '上傳時間';


--
-- Name: COLUMN thread_series_no_abn.thread_code; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.thread_series_no_abn.thread_code IS '牙板編號';


--
-- Name: COLUMN thread_series_no_abn.thread_series_no; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.thread_series_no_abn.thread_series_no IS '牙板序號';


--
-- Name: COLUMN thread_series_no_abn.work_code; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.thread_series_no_abn.work_code IS '派工單號';


--
-- Name: COLUMN thread_series_no_abn.thread_err_code; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.thread_series_no_abn.thread_err_code IS '牙板異常編號';


--
-- Name: COLUMN thread_series_no_abn.creator; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.thread_series_no_abn.creator IS '建立者';


--
-- Name: thread_series_no_use; Type: TABLE; Schema: public; Owner: paas
--

CREATE TABLE public.thread_series_no_use (
    upload_at timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL,
    thread_code character varying(50),
    thread_series_no character varying(50),
    work_code character varying(50),
    status character varying(1),
    cnt integer,
    creator integer
);


ALTER TABLE public.thread_series_no_use OWNER TO paas;

--
-- Name: TABLE thread_series_no_use; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON TABLE public.thread_series_no_use IS '牙板上/下線紀錄資料表';


--
-- Name: COLUMN thread_series_no_use.upload_at; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.thread_series_no_use.upload_at IS '上傳時間';


--
-- Name: COLUMN thread_series_no_use.thread_code; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.thread_series_no_use.thread_code IS '牙板編號';


--
-- Name: COLUMN thread_series_no_use.thread_series_no; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.thread_series_no_use.thread_series_no IS '牙板序號';


--
-- Name: COLUMN thread_series_no_use.work_code; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.thread_series_no_use.work_code IS '派工單號';


--
-- Name: COLUMN thread_series_no_use.status; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.thread_series_no_use.status IS '狀態(S=開始,E=結束)';


--
-- Name: COLUMN thread_series_no_use.cnt; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.thread_series_no_use.cnt IS '支數';


--
-- Name: COLUMN thread_series_no_use.creator; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.thread_series_no_use.creator IS '建立者';


--
-- Name: wire_scroll_no_abn; Type: TABLE; Schema: public; Owner: paas
--

CREATE TABLE public.wire_scroll_no_abn (
    upload_at timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL,
    scroll_no character varying(50),
    work_code character varying(50),
    wire_err_code character varying(20),
    creator integer
);


ALTER TABLE public.wire_scroll_no_abn OWNER TO paas;

--
-- Name: TABLE wire_scroll_no_abn; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON TABLE public.wire_scroll_no_abn IS '線材異常紀錄資料表';


--
-- Name: COLUMN wire_scroll_no_abn.upload_at; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.wire_scroll_no_abn.upload_at IS '上傳時間';


--
-- Name: COLUMN wire_scroll_no_abn.scroll_no; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.wire_scroll_no_abn.scroll_no IS '線材捲號';


--
-- Name: COLUMN wire_scroll_no_abn.work_code; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.wire_scroll_no_abn.work_code IS '派工單號';


--
-- Name: COLUMN wire_scroll_no_abn.wire_err_code; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.wire_scroll_no_abn.wire_err_code IS '線材異常編號';


--
-- Name: COLUMN wire_scroll_no_abn.creator; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.wire_scroll_no_abn.creator IS '建立者';


--
-- Name: wire_scroll_no_use; Type: TABLE; Schema: public; Owner: paas
--

CREATE TABLE public.wire_scroll_no_use (
    upload_at timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL,
    scroll_no character varying(50),
    work_code character varying(50),
    status character varying(1),
    weight numeric(18,5),
    creator integer
);


ALTER TABLE public.wire_scroll_no_use OWNER TO paas;

--
-- Name: TABLE wire_scroll_no_use; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON TABLE public.wire_scroll_no_use IS '線材上/下線紀錄資料表表';


--
-- Name: COLUMN wire_scroll_no_use.upload_at; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.wire_scroll_no_use.upload_at IS '上傳時間';


--
-- Name: COLUMN wire_scroll_no_use.scroll_no; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.wire_scroll_no_use.scroll_no IS '線材捲號';


--
-- Name: COLUMN wire_scroll_no_use.work_code; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.wire_scroll_no_use.work_code IS '派工單號';


--
-- Name: COLUMN wire_scroll_no_use.status; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.wire_scroll_no_use.status IS '狀態(S=開始,E=結束)';


--
-- Name: COLUMN wire_scroll_no_use.weight; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.wire_scroll_no_use.weight IS '線材重量(剩餘重)';


--
-- Name: COLUMN wire_scroll_no_use.creator; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.wire_scroll_no_use.creator IS '建立者';


--
-- Name: work_code_abn; Type: TABLE; Schema: public; Owner: paas
--

CREATE TABLE public.work_code_abn (
    upload_at timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL,
    work_code character varying(50),
    project_id integer,
    work_err_code character varying(20),
    cnt integer,
    creator integer
);


ALTER TABLE public.work_code_abn OWNER TO paas;

--
-- Name: TABLE work_code_abn; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON TABLE public.work_code_abn IS '工單異常紀錄資料表';


--
-- Name: COLUMN work_code_abn.upload_at; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.work_code_abn.upload_at IS '上傳時間';


--
-- Name: COLUMN work_code_abn.work_code; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.work_code_abn.work_code IS '流程卡號';


--
-- Name: COLUMN work_code_abn.project_id; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.work_code_abn.project_id IS '製程代號';


--
-- Name: COLUMN work_code_abn.work_err_code; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.work_code_abn.work_err_code IS '工單異常編號';


--
-- Name: COLUMN work_code_abn.cnt; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.work_code_abn.cnt IS '暫停支數';


--
-- Name: COLUMN work_code_abn.creator; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.work_code_abn.creator IS '建立者';


--
-- Name: work_code_use; Type: TABLE; Schema: public; Owner: paas
--

CREATE TABLE public.work_code_use (
    upload_at timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL,
    work_code character varying(50),
    project_id integer,
    device_name character varying(50),
    status character varying(1),
    creator integer
);


ALTER TABLE public.work_code_use OWNER TO paas;

--
-- Name: TABLE work_code_use; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON TABLE public.work_code_use IS '工單上/下線紀錄資料表';


--
-- Name: COLUMN work_code_use.upload_at; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.work_code_use.upload_at IS '上傳時間';


--
-- Name: COLUMN work_code_use.work_code; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.work_code_use.work_code IS '派工單號';


--
-- Name: COLUMN work_code_use.project_id; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.work_code_use.project_id IS '製程代號';


--
-- Name: COLUMN work_code_use.device_name; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.work_code_use.device_name IS '設備名稱(F12、F11)';


--
-- Name: COLUMN work_code_use.status; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.work_code_use.status IS '狀態(S=開始,E=結束)';


--
-- Name: COLUMN work_code_use.creator; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.work_code_use.creator IS '建立者';


--
-- Name: workhour; Type: TABLE; Schema: public; Owner: paas
--

CREATE TABLE public.workhour (
    upload_at timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL,
    work_code character varying(50),
    project_id integer,
    status character varying(1),
    creator integer
);


ALTER TABLE public.workhour OWNER TO paas;

--
-- Name: TABLE workhour; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON TABLE public.workhour IS '工時紀錄資料表';


--
-- Name: COLUMN workhour.upload_at; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.workhour.upload_at IS '上傳時間';


--
-- Name: COLUMN workhour.work_code; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.workhour.work_code IS '派工單號';


--
-- Name: COLUMN workhour.project_id; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.workhour.project_id IS '製程代號';


--
-- Name: COLUMN workhour.status; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.workhour.status IS '狀態(S/P/E)(S=開始,P=暫停,E=結束)';


--
-- Name: COLUMN workhour.creator; Type: COMMENT; Schema: public; Owner: paas
--

COMMENT ON COLUMN public.workhour.creator IS '建立者';


--
-- Name: seq; Type: DEFAULT; Schema: public; Owner: paas
--

ALTER TABLE ONLY public.test ALTER COLUMN seq SET DEFAULT nextval('public.test_seq_seq'::regclass);


--
-- Name: f11_pkey; Type: CONSTRAINT; Schema: public; Owner: paas
--

ALTER TABLE ONLY public.f11
    ADD CONSTRAINT f11_pkey PRIMARY KEY (upload_at);


--
-- Name: f123_pkey; Type: CONSTRAINT; Schema: public; Owner: paas
--

ALTER TABLE ONLY public.f123
    ADD CONSTRAINT f123_pkey PRIMARY KEY (upload_at);


--
-- Name: f12_pkey; Type: CONSTRAINT; Schema: public; Owner: paas
--

ALTER TABLE ONLY public.f12
    ADD CONSTRAINT f12_pkey PRIMARY KEY (upload_at);


--
-- Name: inspection_pkey; Type: CONSTRAINT; Schema: public; Owner: paas
--

ALTER TABLE ONLY public.inspection
    ADD CONSTRAINT inspection_pkey PRIMARY KEY (upload_at);


--
-- Name: machine_alarm_video_pkey; Type: CONSTRAINT; Schema: public; Owner: paas
--

ALTER TABLE ONLY public.machine_alarm_video
    ADD CONSTRAINT machine_alarm_video_pkey PRIMARY KEY (upload_at);


--
-- Name: machine_on_off_hist_pkey; Type: CONSTRAINT; Schema: public; Owner: paas
--

ALTER TABLE ONLY public.machine_on_off_hist
    ADD CONSTRAINT machine_on_off_hist_pkey PRIMARY KEY (upload_at, device_name);


--
-- Name: machine_status_sum_pkey; Type: CONSTRAINT; Schema: public; Owner: paas
--

ALTER TABLE ONLY public.machine_status_sum
    ADD CONSTRAINT machine_status_sum_pkey PRIMARY KEY (upload_at, device_name);


--
-- Name: material_batch_no_abn_pkey; Type: CONSTRAINT; Schema: public; Owner: paas
--

ALTER TABLE ONLY public.material_batch_no_abn
    ADD CONSTRAINT material_batch_no_abn_pkey PRIMARY KEY (upload_at);


--
-- Name: material_batch_no_use_pkey; Type: CONSTRAINT; Schema: public; Owner: paas
--

ALTER TABLE ONLY public.material_batch_no_use
    ADD CONSTRAINT material_batch_no_use_pkey PRIMARY KEY (upload_at);


--
-- Name: mould_series_no_abn_pkey; Type: CONSTRAINT; Schema: public; Owner: paas
--

ALTER TABLE ONLY public.mould_series_no_abn
    ADD CONSTRAINT mould_series_no_abn_pkey PRIMARY KEY (upload_at);


--
-- Name: mould_series_no_use_pkey; Type: CONSTRAINT; Schema: public; Owner: paas
--

ALTER TABLE ONLY public.mould_series_no_use
    ADD CONSTRAINT mould_series_no_use_pkey PRIMARY KEY (upload_at);


--
-- Name: r09_pkey; Type: CONSTRAINT; Schema: public; Owner: paas
--

ALTER TABLE ONLY public.r09
    ADD CONSTRAINT r09_pkey PRIMARY KEY (upload_at);


--
-- Name: runcard_pkey; Type: CONSTRAINT; Schema: public; Owner: paas
--

ALTER TABLE ONLY public.runcard
    ADD CONSTRAINT runcard_pkey PRIMARY KEY (upload_at);


--
-- Name: runcard_thread_hist_pkey; Type: CONSTRAINT; Schema: public; Owner: paas
--

ALTER TABLE ONLY public.runcard_thread_hist
    ADD CONSTRAINT runcard_thread_hist_pkey PRIMARY KEY (upload_at, runcard_code);


--
-- Name: test_pkey; Type: CONSTRAINT; Schema: public; Owner: paas
--

ALTER TABLE ONLY public.test
    ADD CONSTRAINT test_pkey PRIMARY KEY (seq);


--
-- Name: thread_series_no_abn_pkey; Type: CONSTRAINT; Schema: public; Owner: paas
--

ALTER TABLE ONLY public.thread_series_no_abn
    ADD CONSTRAINT thread_series_no_abn_pkey PRIMARY KEY (upload_at);


--
-- Name: thread_series_no_use_pkey; Type: CONSTRAINT; Schema: public; Owner: paas
--

ALTER TABLE ONLY public.thread_series_no_use
    ADD CONSTRAINT thread_series_no_use_pkey PRIMARY KEY (upload_at);


--
-- Name: wire_scroll_no_abn_pkey; Type: CONSTRAINT; Schema: public; Owner: paas
--

ALTER TABLE ONLY public.wire_scroll_no_abn
    ADD CONSTRAINT wire_scroll_no_abn_pkey PRIMARY KEY (upload_at);


--
-- Name: wire_scroll_no_use_pkey; Type: CONSTRAINT; Schema: public; Owner: paas
--

ALTER TABLE ONLY public.wire_scroll_no_use
    ADD CONSTRAINT wire_scroll_no_use_pkey PRIMARY KEY (upload_at);


--
-- Name: work_code_abn_pkey; Type: CONSTRAINT; Schema: public; Owner: paas
--

ALTER TABLE ONLY public.work_code_abn
    ADD CONSTRAINT work_code_abn_pkey PRIMARY KEY (upload_at);


--
-- Name: work_code_use_pkey; Type: CONSTRAINT; Schema: public; Owner: paas
--

ALTER TABLE ONLY public.work_code_use
    ADD CONSTRAINT work_code_use_pkey PRIMARY KEY (upload_at);


--
-- Name: workhour_pkey; Type: CONSTRAINT; Schema: public; Owner: paas
--

ALTER TABLE ONLY public.workhour
    ADD CONSTRAINT workhour_pkey PRIMARY KEY (upload_at);


--
-- Name: SCHEMA public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

