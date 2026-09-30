--
-- PostgreSQL database dump
--

\restrict ycKleqDS5k8QLeneFSKgGBddibW2F1bf6k231an58VQn4qSadReuotuNgi2GHbp

-- Dumped from database version 18.3
-- Dumped by pg_dump version 18.3

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: achievements; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.achievements (
    id bigint NOT NULL,
    profile_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    scale character varying(255) NOT NULL,
    month character varying(255) NOT NULL,
    year integer NOT NULL,
    description text,
    certificate_path character varying(255)
);


ALTER TABLE public.achievements OWNER TO postgres;

--
-- Name: achievements_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.achievements_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.achievements_id_seq OWNER TO postgres;

--
-- Name: achievements_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.achievements_id_seq OWNED BY public.achievements.id;


--
-- Name: applicant_families; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.applicant_families (
    id bigint NOT NULL,
    applicant_profile_id bigint NOT NULL,
    father_name character varying(255),
    father_birth_year integer,
    father_last_education character varying(255),
    father_occupation character varying(255),
    father_company character varying(255),
    mother_name character varying(255),
    mother_birth_year integer,
    mother_last_education character varying(255),
    mother_occupation character varying(255),
    mother_company character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.applicant_families OWNER TO postgres;

--
-- Name: applicant_families_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.applicant_families_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.applicant_families_id_seq OWNER TO postgres;

--
-- Name: applicant_families_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.applicant_families_id_seq OWNED BY public.applicant_families.id;


--
-- Name: applicant_job_preferences; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.applicant_job_preferences (
    id bigint NOT NULL,
    applicant_profile_id bigint NOT NULL,
    interested_field_1 character varying(255),
    interested_field_2 character varying(255),
    interested_field_3 character varying(255),
    notice_period character varying(255),
    expected_salary numeric(15,2),
    is_willing_to_relocate boolean DEFAULT false NOT NULL,
    job_search_status character varying(255),
    notification_period character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.applicant_job_preferences OWNER TO postgres;

--
-- Name: applicant_job_preferences_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.applicant_job_preferences_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.applicant_job_preferences_id_seq OWNER TO postgres;

--
-- Name: applicant_job_preferences_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.applicant_job_preferences_id_seq OWNED BY public.applicant_job_preferences.id;


--
-- Name: applicant_profile; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.applicant_profile (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    nik character varying(255),
    full_name character varying(255),
    gender character varying(255),
    birth_place character varying(255),
    birth_date date,
    phone character varying(255),
    address text,
    city character varying(255),
    province character varying(255),
    photo character varying(255),
    npwp character varying(255),
    about_me text,
    generated_cv_url character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    child_sequence integer,
    total_siblings integer,
    marital_status character varying(255),
    cv_file_path character varying(255),
    CONSTRAINT applicant_profile_marital_status_check CHECK (((marital_status)::text = ANY ((ARRAY['lajang'::character varying, 'menikah'::character varying, 'bercerai'::character varying])::text[])))
);


ALTER TABLE public.applicant_profile OWNER TO postgres;

--
-- Name: applicant_profile_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.applicant_profile_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.applicant_profile_id_seq OWNER TO postgres;

--
-- Name: applicant_profile_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.applicant_profile_id_seq OWNED BY public.applicant_profile.id;


--
-- Name: application_status_history; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.application_status_history (
    id bigint NOT NULL,
    job_applications_id bigint NOT NULL,
    status character varying(255) NOT NULL,
    notes text,
    changed_by bigint NOT NULL,
    changed_at timestamp(0) without time zone NOT NULL
);


ALTER TABLE public.application_status_history OWNER TO postgres;

--
-- Name: application_status_history_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.application_status_history_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.application_status_history_id_seq OWNER TO postgres;

--
-- Name: application_status_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.application_status_history_id_seq OWNED BY public.application_status_history.id;


--
-- Name: cache; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration bigint NOT NULL
);


ALTER TABLE public.cache OWNER TO postgres;

--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration bigint NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO postgres;

--
-- Name: certifications; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.certifications (
    id bigint NOT NULL,
    profile_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    certificate_path character varying(255)
);


ALTER TABLE public.certifications OWNER TO postgres;

--
-- Name: certifications_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.certifications_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.certifications_id_seq OWNER TO postgres;

--
-- Name: certifications_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.certifications_id_seq OWNED BY public.certifications.id;


--
-- Name: companies; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.companies (
    id bigint NOT NULL,
    role_id bigint,
    name character varying(255) NOT NULL,
    logo character varying(255),
    website character varying(255),
    address text,
    city character varying(255),
    province character varying(255),
    tagline character varying(255),
    about text,
    vision text,
    missions json,
    core_values json,
    postal_code character varying(20),
    phone character varying(50),
    email character varying(255)
);


ALTER TABLE public.companies OWNER TO postgres;

--
-- Name: companies_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.companies_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.companies_id_seq OWNER TO postgres;

--
-- Name: companies_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.companies_id_seq OWNED BY public.companies.id;


--
-- Name: company_showcases; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.company_showcases (
    id bigint NOT NULL,
    company_id bigint,
    tag character varying(255),
    title character varying(255) NOT NULL,
    description text,
    img1 character varying(255) NOT NULL,
    img2 character varying(255),
    img3 character varying(255),
    badge_title character varying(255) DEFAULT 'EVENT'::character varying,
    badge_sub character varying(255),
    "order" integer DEFAULT 0 NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.company_showcases OWNER TO postgres;

--
-- Name: company_showcases_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.company_showcases_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.company_showcases_id_seq OWNER TO postgres;

--
-- Name: company_showcases_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.company_showcases_id_seq OWNED BY public.company_showcases.id;


--
-- Name: degrees; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.degrees (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    rank integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.degrees OWNER TO postgres;

--
-- Name: COLUMN degrees.rank; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.degrees.rank IS 'Angka urutan tingkatan pendidikan';


--
-- Name: degrees_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.degrees_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.degrees_id_seq OWNER TO postgres;

--
-- Name: degrees_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.degrees_id_seq OWNED BY public.degrees.id;


--
-- Name: department_test; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.department_test (
    id bigint NOT NULL,
    test_id bigint NOT NULL,
    department_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.department_test OWNER TO postgres;

--
-- Name: department_test_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.department_test_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.department_test_id_seq OWNER TO postgres;

--
-- Name: department_test_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.department_test_id_seq OWNED BY public.department_test.id;


--
-- Name: departments; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.departments (
    id bigint NOT NULL,
    company_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    description text
);


ALTER TABLE public.departments OWNER TO postgres;

--
-- Name: departments_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.departments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.departments_id_seq OWNER TO postgres;

--
-- Name: departments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.departments_id_seq OWNED BY public.departments.id;


--
-- Name: disc_norms; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.disc_norms (
    id bigint NOT NULL,
    line_type smallint NOT NULL,
    attribute character(10) NOT NULL,
    raw_score integer NOT NULL,
    converted_score double precision NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.disc_norms OWNER TO postgres;

--
-- Name: disc_norms_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.disc_norms_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.disc_norms_id_seq OWNER TO postgres;

--
-- Name: disc_norms_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.disc_norms_id_seq OWNED BY public.disc_norms.id;


--
-- Name: disc_profiles; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.disc_profiles (
    id bigint NOT NULL,
    pattern_code character varying(255) NOT NULL,
    title character varying(255) NOT NULL,
    general_description text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    suitable_jobs text
);


ALTER TABLE public.disc_profiles OWNER TO postgres;

--
-- Name: disc_profiles_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.disc_profiles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.disc_profiles_id_seq OWNER TO postgres;

--
-- Name: disc_profiles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.disc_profiles_id_seq OWNED BY public.disc_profiles.id;


--
-- Name: disc_test_results; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.disc_test_results (
    id bigint NOT NULL,
    test_attempt_id bigint NOT NULL,
    disc_profiles_id bigint,
    line_1_scores json,
    line_2_scores json,
    line_3_scores json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.disc_test_results OWNER TO postgres;

--
-- Name: disc_test_results_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.disc_test_results_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.disc_test_results_id_seq OWNER TO postgres;

--
-- Name: disc_test_results_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.disc_test_results_id_seq OWNED BY public.disc_test_results.id;


--
-- Name: disc_traits; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.disc_traits (
    id bigint NOT NULL,
    dimension_code character(10) NOT NULL,
    potret_diri json,
    kelebihan json,
    kekurangan json,
    deskripsi_tipe text,
    kecenderungan json,
    lingkungan_cocok json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.disc_traits OWNER TO postgres;

--
-- Name: disc_traits_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.disc_traits_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.disc_traits_id_seq OWNER TO postgres;

--
-- Name: disc_traits_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.disc_traits_id_seq OWNED BY public.disc_traits.id;


--
-- Name: educations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.educations (
    id bigint NOT NULL,
    profile_id bigint NOT NULL,
    school_name character varying(255) NOT NULL,
    degree character varying(255),
    major character varying(255),
    study_program character varying(255),
    start_year integer NOT NULL,
    end_year integer,
    gpa numeric(5,2),
    description text,
    degree_id bigint,
    major_id bigint
);


ALTER TABLE public.educations OWNER TO postgres;

--
-- Name: educations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.educations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.educations_id_seq OWNER TO postgres;

--
-- Name: educations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.educations_id_seq OWNED BY public.educations.id;


--
-- Name: employee_profiles; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.employee_profiles (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    nik character varying(20),
    full_name character varying(255) NOT NULL,
    company_id bigint,
    department_id bigint,
    position_title character varying(255),
    phone_number character varying(25),
    gender character varying(255),
    birth_date date,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    photo character varying(255),
    employee_type character varying(255) DEFAULT 'permanent'::character varying NOT NULL,
    position_id bigint,
    CONSTRAINT employee_profiles_employee_type_check CHECK (((employee_type)::text = ANY ((ARRAY['permanent'::character varying, 'contract'::character varying, 'internship'::character varying, 'probation'::character varying])::text[]))),
    CONSTRAINT employee_profiles_gender_check CHECK (((gender)::text = ANY ((ARRAY['male'::character varying, 'female'::character varying])::text[])))
);


ALTER TABLE public.employee_profiles OWNER TO postgres;

--
-- Name: employee_profiles_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.employee_profiles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.employee_profiles_id_seq OWNER TO postgres;

--
-- Name: employee_profiles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.employee_profiles_id_seq OWNED BY public.employee_profiles.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection character varying(255) NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.failed_jobs_id_seq OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: interview_schedule; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.interview_schedule (
    id bigint NOT NULL,
    job_applications_id bigint NOT NULL,
    users_id bigint NOT NULL,
    interview_date timestamp(0) without time zone NOT NULL,
    location character varying(255) NOT NULL,
    meeting_link character varying(255),
    status character varying(255) NOT NULL
);


ALTER TABLE public.interview_schedule OWNER TO postgres;

--
-- Name: interview_schedule_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.interview_schedule_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.interview_schedule_id_seq OWNER TO postgres;

--
-- Name: interview_schedule_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.interview_schedule_id_seq OWNED BY public.interview_schedule.id;


--
-- Name: job_applications; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.job_applications (
    id bigint NOT NULL,
    job_id bigint NOT NULL,
    profile_id bigint NOT NULL,
    status character varying(255) NOT NULL,
    applied_at timestamp(0) without time zone NOT NULL,
    notes text,
    recruiter_approval character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    recruiter_notes text,
    recruiter_approved_at timestamp(0) without time zone,
    recruiter_id bigint,
    admin_approval character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    admin_notes text,
    admin_approved_at timestamp(0) without time zone,
    admin_id bigint
);


ALTER TABLE public.job_applications OWNER TO postgres;

--
-- Name: job_applications_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.job_applications_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.job_applications_id_seq OWNER TO postgres;

--
-- Name: job_applications_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.job_applications_id_seq OWNED BY public.job_applications.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


ALTER TABLE public.job_batches OWNER TO postgres;

--
-- Name: job_degrees; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.job_degrees (
    id bigint NOT NULL,
    job_id bigint NOT NULL,
    degree_id bigint NOT NULL
);


ALTER TABLE public.job_degrees OWNER TO postgres;

--
-- Name: job_degrees_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.job_degrees_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.job_degrees_id_seq OWNER TO postgres;

--
-- Name: job_degrees_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.job_degrees_id_seq OWNED BY public.job_degrees.id;


--
-- Name: job_majors; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.job_majors (
    id bigint NOT NULL,
    job_id bigint NOT NULL,
    major_id bigint NOT NULL
);


ALTER TABLE public.job_majors OWNER TO postgres;

--
-- Name: job_majors_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.job_majors_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.job_majors_id_seq OWNER TO postgres;

--
-- Name: job_majors_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.job_majors_id_seq OWNED BY public.job_majors.id;


--
-- Name: job_test; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.job_test (
    id bigint NOT NULL,
    test_id bigint NOT NULL,
    job_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.job_test OWNER TO postgres;

--
-- Name: job_test_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.job_test_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.job_test_id_seq OWNER TO postgres;

--
-- Name: job_test_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.job_test_id_seq OWNED BY public.job_test.id;


--
-- Name: jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    company_id bigint NOT NULL,
    department_id bigint NOT NULL,
    title character varying(255) NOT NULL,
    description text NOT NULL,
    employment_type character varying(255) NOT NULL,
    location character varying(255) NOT NULL,
    salary_min numeric(15,2),
    salary_max numeric(15,2),
    quota integer NOT NULL,
    deadline date NOT NULL,
    status character varying(255) NOT NULL,
    position_id bigint,
    reviewer_id bigint
);


ALTER TABLE public.jobs OWNER TO postgres;

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jobs_id_seq OWNER TO postgres;

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: languages; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.languages (
    id bigint NOT NULL,
    profile_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    certificate_path character varying(255)
);


ALTER TABLE public.languages OWNER TO postgres;

--
-- Name: languages_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.languages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.languages_id_seq OWNER TO postgres;

--
-- Name: languages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.languages_id_seq OWNED BY public.languages.id;


--
-- Name: majors; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.majors (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.majors OWNER TO postgres;

--
-- Name: majors_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.majors_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.majors_id_seq OWNER TO postgres;

--
-- Name: majors_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.majors_id_seq OWNED BY public.majors.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: organizations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.organizations (
    id bigint NOT NULL,
    profile_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    "position" character varying(255) NOT NULL,
    description text,
    is_active boolean DEFAULT false NOT NULL,
    start_month character varying(255) NOT NULL,
    start_year integer NOT NULL,
    end_month character varying(255),
    end_year integer
);


ALTER TABLE public.organizations OWNER TO postgres;

--
-- Name: organizations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.organizations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.organizations_id_seq OWNER TO postgres;

--
-- Name: organizations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.organizations_id_seq OWNED BY public.organizations.id;


--
-- Name: papi_aspects; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.papi_aspects (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    english_name character varying(255),
    order_number smallint DEFAULT '1'::smallint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.papi_aspects OWNER TO postgres;

--
-- Name: papi_aspects_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.papi_aspects_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.papi_aspects_id_seq OWNER TO postgres;

--
-- Name: papi_aspects_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.papi_aspects_id_seq OWNED BY public.papi_aspects.id;


--
-- Name: papi_factors; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.papi_factors (
    id bigint NOT NULL,
    aspect_id bigint NOT NULL,
    code character varying(2) NOT NULL,
    name character varying(255) NOT NULL,
    english_name character varying(255),
    type character varying(255) NOT NULL,
    description text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT papi_factors_type_check CHECK (((type)::text = ANY ((ARRAY['role'::character varying, 'need'::character varying])::text[])))
);


ALTER TABLE public.papi_factors OWNER TO postgres;

--
-- Name: COLUMN papi_factors.type; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.papi_factors.type IS 'role = Total Atas (45), need = Total Bawah (45)';


--
-- Name: papi_factors_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.papi_factors_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.papi_factors_id_seq OWNER TO postgres;

--
-- Name: papi_factors_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.papi_factors_id_seq OWNED BY public.papi_factors.id;


--
-- Name: papi_norms; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.papi_norms (
    id bigint NOT NULL,
    factor_id bigint NOT NULL,
    factor_code character varying(2) NOT NULL,
    min_score smallint NOT NULL,
    max_score smallint NOT NULL,
    interpretation text NOT NULL,
    description text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.papi_norms OWNER TO postgres;

--
-- Name: papi_norms_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.papi_norms_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.papi_norms_id_seq OWNER TO postgres;

--
-- Name: papi_norms_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.papi_norms_id_seq OWNED BY public.papi_norms.id;


--
-- Name: papi_test_results; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.papi_test_results (
    id bigint NOT NULL,
    test_attempt_id bigint NOT NULL,
    scores json NOT NULL,
    role_score smallint DEFAULT '0'::smallint NOT NULL,
    need_score smallint DEFAULT '0'::smallint NOT NULL,
    is_valid boolean DEFAULT false NOT NULL,
    interpretations json,
    raw_answers json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.papi_test_results OWNER TO postgres;

--
-- Name: COLUMN papi_test_results.scores; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.papi_test_results.scores IS 'Nilai 20 aspek PAPI (N, G, A, L, P, I, T, V, X, S, B, O, R, D, C, Z, E, K, F, W)';


--
-- Name: COLUMN papi_test_results.role_score; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.papi_test_results.role_score IS 'Total Atas (G, L, I, T, V, S, R, D, C, E) - Normal = 45';


--
-- Name: COLUMN papi_test_results.need_score; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.papi_test_results.need_score IS 'Total Bawah (N, A, P, X, B, O, Z, K, F, W) - Normal = 45';


--
-- Name: COLUMN papi_test_results.is_valid; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.papi_test_results.is_valid IS 'Valid jika role_score == 45 dan need_score == 45';


--
-- Name: COLUMN papi_test_results.interpretations; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.papi_test_results.interpretations IS 'Interpretasi per aspek';


--
-- Name: COLUMN papi_test_results.raw_answers; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.papi_test_results.raw_answers IS 'Jawaban pilihan a/b tiap nomor (1-90)';


--
-- Name: papi_test_results_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.papi_test_results_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.papi_test_results_id_seq OWNER TO postgres;

--
-- Name: papi_test_results_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.papi_test_results_id_seq OWNED BY public.papi_test_results.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO postgres;

--
-- Name: positions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.positions (
    id bigint NOT NULL,
    department_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.positions OWNER TO postgres;

--
-- Name: positions_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.positions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.positions_id_seq OWNER TO postgres;

--
-- Name: positions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.positions_id_seq OWNED BY public.positions.id;


--
-- Name: question_banks; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.question_banks (
    id bigint NOT NULL,
    category_id bigint NOT NULL,
    question text NOT NULL,
    question_type character varying(255) DEFAULT 'multiple_choice'::character varying NOT NULL,
    metadata json,
    image_path character varying(255),
    points integer DEFAULT 1 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.question_banks OWNER TO postgres;

--
-- Name: question_banks_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.question_banks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.question_banks_id_seq OWNER TO postgres;

--
-- Name: question_banks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.question_banks_id_seq OWNED BY public.question_banks.id;


--
-- Name: question_options; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.question_options (
    id bigint NOT NULL,
    question_id bigint NOT NULL,
    option_text text NOT NULL,
    attribute_tag character varying(255),
    is_correct boolean DEFAULT false NOT NULL,
    most_tag character varying(10),
    least_tag character varying(10)
);


ALTER TABLE public.question_options OWNER TO postgres;

--
-- Name: question_options_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.question_options_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.question_options_id_seq OWNER TO postgres;

--
-- Name: question_options_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.question_options_id_seq OWNED BY public.question_options.id;


--
-- Name: queue_jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.queue_jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.queue_jobs OWNER TO postgres;

--
-- Name: queue_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.queue_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.queue_jobs_id_seq OWNER TO postgres;

--
-- Name: queue_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.queue_jobs_id_seq OWNED BY public.queue_jobs.id;


--
-- Name: roles; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    name character varying(255) NOT NULL
);


ALTER TABLE public.roles OWNER TO postgres;

--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.roles_id_seq OWNER TO postgres;

--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO postgres;

--
-- Name: skills; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.skills (
    id bigint NOT NULL,
    profile_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    certificate_path character varying(255)
);


ALTER TABLE public.skills OWNER TO postgres;

--
-- Name: skills_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.skills_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.skills_id_seq OWNER TO postgres;

--
-- Name: skills_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.skills_id_seq OWNED BY public.skills.id;


--
-- Name: social_medias; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.social_medias (
    id bigint NOT NULL,
    profile_id bigint NOT NULL,
    platform_name character varying(255) NOT NULL,
    url character varying(255) NOT NULL
);


ALTER TABLE public.social_medias OWNER TO postgres;

--
-- Name: social_medias_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.social_medias_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.social_medias_id_seq OWNER TO postgres;

--
-- Name: social_medias_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.social_medias_id_seq OWNED BY public.social_medias.id;


--
-- Name: test_answers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.test_answers (
    id bigint NOT NULL,
    attempt_id bigint NOT NULL,
    question_id bigint NOT NULL,
    option_id bigint,
    answer_type character varying(255),
    essay_answer text,
    score numeric(5,2),
    reviewed_by bigint,
    attachment_url text,
    attachment_name character varying(255),
    attachment_size bigint
);


ALTER TABLE public.test_answers OWNER TO postgres;

--
-- Name: test_answers_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.test_answers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.test_answers_id_seq OWNER TO postgres;

--
-- Name: test_answers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.test_answers_id_seq OWNED BY public.test_answers.id;


--
-- Name: test_attempts; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.test_attempts (
    id bigint NOT NULL,
    job_application_id bigint,
    test_id bigint NOT NULL,
    started_at timestamp(0) without time zone,
    finished_at timestamp(0) without time zone,
    duration integer,
    objective_score numeric(5,2),
    essay_score numeric(5,2),
    total_score numeric(5,2),
    status character varying(255) DEFAULT 'in_progress'::character varying NOT NULL,
    user_id bigint,
    attempt_type character varying(255) DEFAULT 'applicant'::character varying NOT NULL,
    participant_name character varying(255),
    participant_age integer,
    participant_gender character varying(255),
    test_date date,
    CONSTRAINT test_attempts_participant_gender_check CHECK (((participant_gender)::text = ANY ((ARRAY['male'::character varying, 'female'::character varying])::text[])))
);


ALTER TABLE public.test_attempts OWNER TO postgres;

--
-- Name: test_attempts_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.test_attempts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.test_attempts_id_seq OWNER TO postgres;

--
-- Name: test_attempts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.test_attempts_id_seq OWNED BY public.test_attempts.id;


--
-- Name: test_categories; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.test_categories (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.test_categories OWNER TO postgres;

--
-- Name: test_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.test_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.test_categories_id_seq OWNER TO postgres;

--
-- Name: test_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.test_categories_id_seq OWNED BY public.test_categories.id;


--
-- Name: test_questions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.test_questions (
    id bigint NOT NULL,
    test_id bigint NOT NULL,
    question_id bigint NOT NULL,
    order_number integer DEFAULT 1 NOT NULL
);


ALTER TABLE public.test_questions OWNER TO postgres;

--
-- Name: test_questions_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.test_questions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.test_questions_id_seq OWNER TO postgres;

--
-- Name: test_questions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.test_questions_id_seq OWNED BY public.test_questions.id;


--
-- Name: tests; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tests (
    id bigint NOT NULL,
    job_id bigint,
    category_id bigint NOT NULL,
    title character varying(255) NOT NULL,
    duration_minutes integer NOT NULL,
    passing_score numeric(5,2) DEFAULT '0'::numeric NOT NULL,
    total_questions integer DEFAULT 0 NOT NULL,
    is_random boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    test_type character varying(255) DEFAULT 'recruitment'::character varying NOT NULL,
    department_id bigint,
    target_employee_type character varying(30) DEFAULT 'all'::character varying NOT NULL
);


ALTER TABLE public.tests OWNER TO postgres;

--
-- Name: tests_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tests_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tests_id_seq OWNER TO postgres;

--
-- Name: tests_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tests_id_seq OWNED BY public.tests.id;


--
-- Name: trainings; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.trainings (
    id bigint NOT NULL,
    profile_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    certificate_path character varying(255)
);


ALTER TABLE public.trainings OWNER TO postgres;

--
-- Name: trainings_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.trainings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.trainings_id_seq OWNER TO postgres;

--
-- Name: trainings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.trainings_id_seq OWNED BY public.trainings.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    role_id bigint NOT NULL,
    nik character varying(255),
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255),
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    google_id character varying(255),
    avatar character varying(255),
    is_recruiter boolean DEFAULT false NOT NULL
);


ALTER TABLE public.users OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: work_experiences; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.work_experiences (
    id bigint NOT NULL,
    profile_id bigint NOT NULL,
    company_name character varying(255) NOT NULL,
    "position" character varying(255) NOT NULL,
    employment_type character varying(255) NOT NULL,
    start_date date NOT NULL,
    end_date date,
    currently_working boolean DEFAULT false NOT NULL,
    description text
);


ALTER TABLE public.work_experiences OWNER TO postgres;

--
-- Name: work_experiences_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.work_experiences_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.work_experiences_id_seq OWNER TO postgres;

--
-- Name: work_experiences_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.work_experiences_id_seq OWNED BY public.work_experiences.id;


--
-- Name: achievements id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.achievements ALTER COLUMN id SET DEFAULT nextval('public.achievements_id_seq'::regclass);


--
-- Name: applicant_families id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.applicant_families ALTER COLUMN id SET DEFAULT nextval('public.applicant_families_id_seq'::regclass);


--
-- Name: applicant_job_preferences id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.applicant_job_preferences ALTER COLUMN id SET DEFAULT nextval('public.applicant_job_preferences_id_seq'::regclass);


--
-- Name: applicant_profile id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.applicant_profile ALTER COLUMN id SET DEFAULT nextval('public.applicant_profile_id_seq'::regclass);


--
-- Name: application_status_history id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.application_status_history ALTER COLUMN id SET DEFAULT nextval('public.application_status_history_id_seq'::regclass);


--
-- Name: certifications id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.certifications ALTER COLUMN id SET DEFAULT nextval('public.certifications_id_seq'::regclass);


--
-- Name: companies id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.companies ALTER COLUMN id SET DEFAULT nextval('public.companies_id_seq'::regclass);


--
-- Name: company_showcases id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.company_showcases ALTER COLUMN id SET DEFAULT nextval('public.company_showcases_id_seq'::regclass);


--
-- Name: degrees id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.degrees ALTER COLUMN id SET DEFAULT nextval('public.degrees_id_seq'::regclass);


--
-- Name: department_test id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.department_test ALTER COLUMN id SET DEFAULT nextval('public.department_test_id_seq'::regclass);


--
-- Name: departments id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.departments ALTER COLUMN id SET DEFAULT nextval('public.departments_id_seq'::regclass);


--
-- Name: disc_norms id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.disc_norms ALTER COLUMN id SET DEFAULT nextval('public.disc_norms_id_seq'::regclass);


--
-- Name: disc_profiles id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.disc_profiles ALTER COLUMN id SET DEFAULT nextval('public.disc_profiles_id_seq'::regclass);


--
-- Name: disc_test_results id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.disc_test_results ALTER COLUMN id SET DEFAULT nextval('public.disc_test_results_id_seq'::regclass);


--
-- Name: disc_traits id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.disc_traits ALTER COLUMN id SET DEFAULT nextval('public.disc_traits_id_seq'::regclass);


--
-- Name: educations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.educations ALTER COLUMN id SET DEFAULT nextval('public.educations_id_seq'::regclass);


--
-- Name: employee_profiles id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.employee_profiles ALTER COLUMN id SET DEFAULT nextval('public.employee_profiles_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: interview_schedule id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.interview_schedule ALTER COLUMN id SET DEFAULT nextval('public.interview_schedule_id_seq'::regclass);


--
-- Name: job_applications id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_applications ALTER COLUMN id SET DEFAULT nextval('public.job_applications_id_seq'::regclass);


--
-- Name: job_degrees id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_degrees ALTER COLUMN id SET DEFAULT nextval('public.job_degrees_id_seq'::regclass);


--
-- Name: job_majors id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_majors ALTER COLUMN id SET DEFAULT nextval('public.job_majors_id_seq'::regclass);


--
-- Name: job_test id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_test ALTER COLUMN id SET DEFAULT nextval('public.job_test_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: languages id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.languages ALTER COLUMN id SET DEFAULT nextval('public.languages_id_seq'::regclass);


--
-- Name: majors id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.majors ALTER COLUMN id SET DEFAULT nextval('public.majors_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: organizations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.organizations ALTER COLUMN id SET DEFAULT nextval('public.organizations_id_seq'::regclass);


--
-- Name: papi_aspects id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.papi_aspects ALTER COLUMN id SET DEFAULT nextval('public.papi_aspects_id_seq'::regclass);


--
-- Name: papi_factors id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.papi_factors ALTER COLUMN id SET DEFAULT nextval('public.papi_factors_id_seq'::regclass);


--
-- Name: papi_norms id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.papi_norms ALTER COLUMN id SET DEFAULT nextval('public.papi_norms_id_seq'::regclass);


--
-- Name: papi_test_results id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.papi_test_results ALTER COLUMN id SET DEFAULT nextval('public.papi_test_results_id_seq'::regclass);


--
-- Name: positions id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.positions ALTER COLUMN id SET DEFAULT nextval('public.positions_id_seq'::regclass);


--
-- Name: question_banks id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.question_banks ALTER COLUMN id SET DEFAULT nextval('public.question_banks_id_seq'::regclass);


--
-- Name: question_options id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.question_options ALTER COLUMN id SET DEFAULT nextval('public.question_options_id_seq'::regclass);


--
-- Name: queue_jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.queue_jobs ALTER COLUMN id SET DEFAULT nextval('public.queue_jobs_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- Name: skills id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.skills ALTER COLUMN id SET DEFAULT nextval('public.skills_id_seq'::regclass);


--
-- Name: social_medias id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.social_medias ALTER COLUMN id SET DEFAULT nextval('public.social_medias_id_seq'::regclass);


--
-- Name: test_answers id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.test_answers ALTER COLUMN id SET DEFAULT nextval('public.test_answers_id_seq'::regclass);


--
-- Name: test_attempts id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.test_attempts ALTER COLUMN id SET DEFAULT nextval('public.test_attempts_id_seq'::regclass);


--
-- Name: test_categories id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.test_categories ALTER COLUMN id SET DEFAULT nextval('public.test_categories_id_seq'::regclass);


--
-- Name: test_questions id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.test_questions ALTER COLUMN id SET DEFAULT nextval('public.test_questions_id_seq'::regclass);


--
-- Name: tests id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tests ALTER COLUMN id SET DEFAULT nextval('public.tests_id_seq'::regclass);


--
-- Name: trainings id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.trainings ALTER COLUMN id SET DEFAULT nextval('public.trainings_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: work_experiences id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.work_experiences ALTER COLUMN id SET DEFAULT nextval('public.work_experiences_id_seq'::regclass);


--
-- Data for Name: achievements; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.achievements (id, profile_id, name, scale, month, year, description, certificate_path) FROM stdin;
\.


--
-- Data for Name: applicant_families; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.applicant_families (id, applicant_profile_id, father_name, father_birth_year, father_last_education, father_occupation, father_company, mother_name, mother_birth_year, mother_last_education, mother_occupation, mother_company, created_at, updated_at) FROM stdin;
1	1	Syauqi	1980	D4/S1	Pegawai Swasta	Berkah Jaya	Lani	1990	D4/S1	Ibu Rumah Tangga	Rumah	2026-08-26 14:07:35	2026-08-26 14:08:45
2	2	Syauqi	1982	D4/S1	PNS / ASN	Perpustakaan	Rani	1993	D4/S1	Buruh / Pekerja Harian	Dunia Tex	2026-09-01 15:09:02	2026-09-01 15:09:02
3	3	Arul	1977	D4/S1	Wiraswasta / Pengusaha	pengusaha catering	Nurul	1985	D4/S1	Tidak Bekerja / Lainnya	tidak bekerja	2026-09-09 20:06:11	2026-09-09 20:06:11
\.


--
-- Data for Name: applicant_job_preferences; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.applicant_job_preferences (id, applicant_profile_id, interested_field_1, interested_field_2, interested_field_3, notice_period, expected_salary, is_willing_to_relocate, job_search_status, notification_period, created_at, updated_at) FROM stdin;
1	1	Marketing	Engineering	Marketing	>= 4 Bulan	20000000.00	t	\N	\N	2026-08-26 14:11:00	2026-08-26 14:11:00
\.


--
-- Data for Name: applicant_profile; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.applicant_profile (id, user_id, nik, full_name, gender, birth_place, birth_date, phone, address, city, province, photo, npwp, about_me, generated_cv_url, created_at, updated_at, child_sequence, total_siblings, marital_status, cv_file_path) FROM stdin;
1	2	3374000011112222	Ilham Taruprasetyo	Laki-laki	Ungaran	2005-06-29	089655493009	Jalan Dabo VII NO 78	KABUPATEN SEMARANG	JAWA TENGAH	\N	\N	\N	\N	2026-08-26 10:42:45	2026-08-26 14:12:13	1	2	lajang	applicant-cvs/yEQ3K7rOU9u86GMEs14EBvNgVypn1FPKT0HcCP7u.pdf
4	17	1234567891234567	budi	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	2026-09-07 13:54:31	2026-09-07 13:54:31	\N	\N	\N	\N
3	16	3322165656560001	Mail Maulana	Laki-laki	Ungaran	\N	089666333221	Jl Serasi Raya	KABUPATEN SEMARANG	JAWA TENGAH	applicant-photos/6aa15bdd63bdd.jpg	\N	Saya suka Coding	\N	2026-09-07 08:18:52	2026-09-09 20:15:11	1	2	lajang	applicant-cvs/ExYOwCBo12d8j1ABg8C5wwMBlmh1iMAvBAQXGzJ3.pdf
2	4	9933220101090002	Mahargya Nashrullah	Laki-laki	Kabupaten Semarang	2009-01-01	089655493009	Jl Dabo 7	KABUPATEN SEMARANG	JAWA TENGAH	applicant-photos/6abb14c786f30.jpg	\N	\N	\N	2026-09-01 10:35:52	2026-09-29 08:30:47	2	2	lajang	applicant-cvs/kmZLlrOQIWYFuuAehZ8RTwmzOt384dV30Sk0xffP.pdf
\.


--
-- Data for Name: application_status_history; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.application_status_history (id, job_applications_id, status, notes, changed_by, changed_at) FROM stdin;
1	1	Submitted	Lamaran pekerjaan berhasil diajukan oleh pelamar.	2	2026-08-26 14:13:09
2	1	Reviewed	\N	1	2026-08-26 14:20:46
3	1	Reviewed	Selamat! Anda lolos seleksi berkas administrasi. Silakan lanjut kerjakan ujian online yang tersedia pada menu Riwayat Lamaran.	1	2026-08-26 14:25:56
4	2	Submitted	Lamaran pekerjaan berhasil diajukan oleh pelamar.	2	2026-08-26 20:59:00
5	2	Reviewed	Selamat! Anda lolos seleksi berkas administrasi. Silakan lanjut kerjakan ujian online yang tersedia pada menu Riwayat Lamaran.	1	2026-08-26 20:59:28
6	3	Submitted	Lamaran pekerjaan berhasil diajukan oleh pelamar.	2	2026-08-26 21:34:28
7	3	Reviewed	Selamat! Anda lolos seleksi berkas administrasi. Silakan lanjut kerjakan ujian online yang tersedia pada menu Riwayat Lamaran.	1	2026-08-26 21:34:44
8	3	Shortlisted	Selamat! Anda lolos seleksi berkas administrasi. Silakan lanjut kerjakan ujian online yang tersedia pada menu Riwayat Lamaran.	1	2026-08-26 22:21:37
9	2	Shortlisted	Selamat! Anda lolos seleksi berkas administrasi. Silakan lanjut kerjakan ujian online yang tersedia pada menu Riwayat Lamaran.	3	2026-08-26 22:27:15
10	1	Reviewed	Ma'ruf C.A	1	2026-08-27 10:03:21
11	1	Shortlisted	Ma'ruf C.A	1	2026-08-27 10:03:45
12	1	Reviewed	Ma'ruf C.A	1	2026-08-27 10:03:56
13	4	Submitted	Lamaran pekerjaan berhasil diajukan oleh pelamar.	2	2026-08-27 10:06:45
14	4	Reviewed	Selamat! Anda lolos seleksi berkas administrasi. Silakan lanjut kerjakan ujian online yang tersedia pada menu Riwayat Lamaran.	1	2026-08-27 10:07:07
15	4	Reviewed	Riza Putri Puspita Dewi	1	2026-08-27 10:13:34
16	1	Reviewed	Ma'ruf Cristi Aji	1	2026-08-27 10:14:15
17	5	Submitted	Lamaran pekerjaan berhasil diajukan oleh pelamar.	4	2026-09-01 15:11:36
18	5	Reviewed	Selamat! Anda lolos seleksi berkas administrasi. Silakan lanjut kerjakan ujian online yang tersedia pada menu Riwayat Lamaran.	1	2026-09-01 15:11:56
19	6	Submitted	Lamaran pekerjaan berhasil diajukan oleh pelamar.	4	2026-09-04 10:54:09
20	6	Reviewed	Selamat! Anda lolos seleksi berkas administrasi. Silakan lanjut kerjakan ujian online yang tersedia pada menu Riwayat Lamaran.	1	2026-09-04 10:54:23
21	7	Submitted	Lamaran pekerjaan berhasil diajukan oleh pelamar.	16	2026-09-09 20:10:41
22	7	Reviewed	Selamat! Anda lolos seleksi berkas administrasi. Silakan lanjut kerjakan ujian online yang tersedia pada menu Riwayat Lamaran.	1	2026-09-09 20:11:04
23	8	Submitted	Lamaran pekerjaan berhasil diajukan oleh pelamar.	4	2026-09-29 08:31:25
\.


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: certifications; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.certifications (id, profile_id, name, certificate_path) FROM stdin;
\.


--
-- Data for Name: companies; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.companies (id, role_id, name, logo, website, address, city, province, tagline, about, vision, missions, core_values, postal_code, phone, email) FROM stdin;
3	1	PT Sapujagat Nirmana Tekna	https://res.cloudinary.com/h1pkmo2b/image/upload/v1788485034/e-recruitment/logos/cbiutn2xnskv7jgouj3a.png	https://www.tekna.id/	Jl. Klipang Ruko Amsterdam No.9D, Sendangmulyo, Kec. Tembalang, Kota Semarang, Jawa Tengah 50272	Semarang	Jawa Tengah	Cloud Platform & IoT Software Solutions	Penyedia ekosistem platform cloud terpadu (Tekna.id), dashboard analitik data sensor real-time, dan solusi perangkat lunak pemantauan industri terhubung.	\N	\N	\N	\N	\N	\N
2	1	PT Autentik Karya Analitika	https://res.cloudinary.com/h1pkmo2b/image/upload/v1788488872/e-recruitment/logos/zyru4pv4dbfbecchj0yi.png	https://www.autentik.co.id/	Jl. Klipang Ruko Amsterdam No.9D, Sendangmulyo, Kec. Tembalang, Kota Semarang, Jawa Tengah 50272	Semarang	Jawa Tengah	Hardware Manufacturing & IoT Devices	PT Autentik Karya Analitika (AKA) adalah perusahaan manufaktur dan pengembang teknologi berbasis Internet of Things (IoT) serta Industri 4.0. Kami berfokus pada penciptaan produk-produk inovatif buatan dalam negeri untuk mendukung kebutuhan pemantauan udara, kualitas air, peralatan laboratorium, dan sistem lingkungan. Mengusung semangat kemandirian industri lokal, AKA menghadirkan perangkat presisi berstandar tinggi yang pintar, akurat, dan dapat dipantau secara langsung (real-time).	\N	[]	[]	\N	082137384029	autentik.info@gmail.com
5	1	PT Berkah Fiber Kreasindo	https://res.cloudinary.com/h1pkmo2b/image/upload/v1789356517/e-recruitment/logos/pilhir28qrxvebghrfji.png	\N	Jl. Klipang Ruko Amsterdam No.9D, Sendangmulyo, Kec. Tembalang, Kota Semarang, Jawa Tengah 50272	Semarang	Jawa Tengah	Manufaktur Casing and Packaging Plastic Moulding Product	PT Berkah Fiber Kreasindo masuk dalam grup Mitra Karya Analitika	\N	[]	[]	\N	\N	\N
4	1	CV Agra Prima Indonesia	https://res.cloudinary.com/h1pkmo2b/image/upload/v1789354712/e-recruitment/logos/x827we6pv7i6i3uj9osz.jpg	\N	Jl. Klipang Ruko Amsterdam No.9D, Sendangmulyo, Kec. Tembalang	Semarang	Jawa Tengah	Penyedia Kebutuhan Sekolah, yayasan dan lembaga pendidikan	Menyediakan perlengkapan dan kebutuhan sekolah, serta beroperasi dalam pengadaan barang lewat platform SIPLah.	\N	[]	[]	\N	\N	\N
1	1	PT Mitra Karya Analitika	logo/0oHm6OKTqU90sYTUgWKhaEx8XPXPOCauzzwMSf66.png	https://mikacares.co.id/	Jl. Klipang Ruko Amsterdam No.9D, Sendangmulyo, Kec. Tembalang	Semarang	Jawa Tengah	The Best Choice for Your Business Partner	PT Mitra Karya Analitika (MIKA) adalah perusahaan terkemuka yang bergerak di bidang distribusi dan penyediaan instrumen laboratorium presisi, peralatan keselamatan dan kesehatan kerja (Health, Safety & Environment / HSE), serta instrumen pemantauan lingkungan (Environmental Monitoring). Berdiri sejak tahun 2014 dan berpusat di Semarang, Jawa Tengah, MIKA telah dipercaya oleh ratusan industri manufaktur, instansi riset, universitas, dan laboratorium pengujian di seluruh penjuru Indonesia sebagai mitra andalan.	Menjadi mitra bisnis terdepan, terpercaya, dan menjadi pilihan utama di Indonesia dalam penyediaan solusi komprehensif peralatan laboratorium, perlindungan keselamatan kerja, dan teknologi pemantauan lingkungan.	["Menyediakan instrumen laboratorium, alat pelindung keselamatan kerja (HSE), dan sistem monitoring lingkungan berkualitas tinggi berstandar internasional.","Memberikan layanan purna jual, kalibrasi, konsultasi teknis terpadu, dan dukungan profesional yang responsif demi kepuasan mitra bisnis.","Membangun ekosistem kemitraan strategis yang berkelanjutan bersama industri manufaktur, institusi riset, akademisi, dan instansi pemerintah di seluruh Indonesia."]	[{"code":"M","title":"Menghargai","subtitle":"Respect","description":"Menjunjung tinggi rasa hormat, menghargai keberagaman pandangan, serta membina komunikasi kerja yang inklusif dan harmonis bagi seluruh insan perusahaan dan mitra.","icon":"hand-shake"},{"code":"I","title":"Integritas","subtitle":"Integrity","description":"Berpikir, berkata, dan bertindak secara jujur, adil, transparan, serta berpegang teguh pada prinsip moral dan kode etik bisnis profesional tanpa kompromi.","icon":"shield-check"},{"code":"K","title":"Komitmen","subtitle":"Commitment","description":"Berdedikasi tinggi untuk memberikan pelayanan berkualitas terbaik, menepati janji kesepakatan, dan terus berinovasi menjawab kebutuhan industri secara berkesinambungan.","icon":"sparkles"},{"code":"A","title":"Akuntabel","subtitle":"Accountable","description":"Bertanggung jawab penuh atas setiap keputusan, tindakan, dan hasil kerja demi menjaga kepercayaan mitra serta pemangku kepentingan secara transparan.","icon":"check-badge"}]	50272	081770555554	info@mikacares.co.id
\.


--
-- Data for Name: company_showcases; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.company_showcases (id, company_id, tag, title, description, img1, img2, img3, badge_title, badge_sub, "order", is_active, created_at, updated_at) FROM stdin;
1	1	Tentang Kami & Karir	Ruang untuk Bertumbuh dan Berkembang	Kami mendorong setiap individu untuk terus berkembang melalui pelatihan berkelanjutan, pengembangan sumber daya manusia, serta lingkungan kerja modern. Bersama Mitra Karya Analitika, kembangkan potensi, pengalaman, dan karier Anda secara optimal.	asset-compro/aniv1.jpg	asset-compro/outbond.jpg	asset-compro/aniv.jpg	CAREER	Growth & Development	1	t	2026-09-30 09:33:19	2026-09-30 09:33:19
2	1	Acara & Kolaborasi	Bimbingan Teknis ASPADIN 2026: Sinergi Kompetensi	Menghadirkan sesi Bimbingan Teknis eksklusif di Semarang bagi para mitra industri. Kami berbagi pengetahuan, memamerkan inovasi solusi IoT terbaru, dan memperkuat jaringan untuk pertumbuhan profesional bersama.	asset-compro/aspadin1.jpg	asset-compro/aspadin2.jpg	asset-compro/aspadin3.jpg	EVENT	Technical Guidance	2	t	2026-09-30 09:33:19	2026-09-30 09:33:19
3	1	Acara & Pameran	Partisipasi Aktif di Event HISFARIN 2025	Memperluas jaringan dan memperkenalkan solusi teknologi analitik terkini dalam Musyawarah Nasional HISFARIN 2025. Kami hadir langsung menyapa para profesional, memamerkan perangkat keras inovatif, dan membangun sinergi kolaboratif untuk mendukung kemajuan industri.	asset-compro/hisfarin1.jpg	asset-compro/hisfarin2.jpg	asset-compro/hisfarin3.jpg	EXHIBITION	HISFARIN 2025	3	t	2026-09-30 09:33:19	2026-09-30 09:33:19
\.


--
-- Data for Name: degrees; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.degrees (id, name, rank, created_at, updated_at) FROM stdin;
1	SMA/SMK	1	2026-08-26 10:42:46	2026-08-26 10:42:46
2	D3	2	2026-08-26 10:42:46	2026-08-26 10:42:46
3	D4/S1	3	2026-08-26 10:42:46	2026-08-26 10:42:46
4	S2	4	2026-08-26 10:42:46	2026-08-26 10:42:46
5	S3	5	2026-08-26 10:42:46	2026-08-26 10:42:46
\.


--
-- Data for Name: department_test; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.department_test (id, test_id, department_id, created_at, updated_at) FROM stdin;
1	5	2	2026-09-04 08:13:47	2026-09-04 08:13:47
2	6	1	2026-09-04 08:13:47	2026-09-04 08:13:47
3	6	4	2026-09-04 08:27:27	2026-09-04 08:27:27
4	6	2	2026-09-04 08:27:28	2026-09-04 08:27:28
5	6	3	2026-09-04 08:27:28	2026-09-04 08:27:28
\.


--
-- Data for Name: departments; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.departments (id, company_id, name, description) FROM stdin;
2	1	Marketing	\N
3	1	Product	\N
4	2	Engineering	Software & Hardware Division
1	1	Finance	Mengatur anggaran, laporan keuangan, dan arus kas perusahaan.
5	4	Marketing	\N
\.


--
-- Data for Name: disc_norms; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.disc_norms (id, line_type, attribute, raw_score, converted_score, created_at, updated_at) FROM stdin;
1	1	D         	0	-6	2026-09-04 08:15:20	2026-09-04 08:15:20
2	1	I         	0	-7	2026-09-04 08:15:20	2026-09-04 08:15:20
3	1	S         	0	-5.7	2026-09-04 08:15:20	2026-09-04 08:15:20
4	1	C         	0	-6	2026-09-04 08:15:20	2026-09-04 08:15:20
5	1	D         	1	-5.3	2026-09-04 08:15:20	2026-09-04 08:15:20
6	1	I         	1	-4.6	2026-09-04 08:15:20	2026-09-04 08:15:20
7	1	S         	1	-4.3	2026-09-04 08:15:20	2026-09-04 08:15:20
8	1	C         	1	-4.7	2026-09-04 08:15:20	2026-09-04 08:15:20
9	1	D         	2	-4	2026-09-04 08:15:20	2026-09-04 08:15:20
10	1	I         	2	-2.5	2026-09-04 08:15:20	2026-09-04 08:15:20
11	1	S         	2	-3.5	2026-09-04 08:15:20	2026-09-04 08:15:20
12	1	C         	2	-3.5	2026-09-04 08:15:20	2026-09-04 08:15:20
13	1	D         	3	-2.5	2026-09-04 08:15:20	2026-09-04 08:15:20
14	1	I         	3	-1.3	2026-09-04 08:15:20	2026-09-04 08:15:20
15	1	S         	3	-1.5	2026-09-04 08:15:20	2026-09-04 08:15:20
16	1	C         	3	-1.5	2026-09-04 08:15:20	2026-09-04 08:15:20
17	1	D         	4	-1.7	2026-09-04 08:15:20	2026-09-04 08:15:20
18	1	I         	4	1	2026-09-04 08:15:20	2026-09-04 08:15:20
19	1	S         	4	-0.7	2026-09-04 08:15:20	2026-09-04 08:15:20
20	1	C         	4	0.5	2026-09-04 08:15:20	2026-09-04 08:15:20
21	1	D         	5	-1.3	2026-09-04 08:15:20	2026-09-04 08:15:20
22	1	I         	5	3	2026-09-04 08:15:20	2026-09-04 08:15:20
23	1	S         	5	0.5	2026-09-04 08:15:20	2026-09-04 08:15:20
24	1	C         	5	2	2026-09-04 08:15:20	2026-09-04 08:15:20
25	1	D         	6	0	2026-09-04 08:15:20	2026-09-04 08:15:20
26	1	I         	6	3.5	2026-09-04 08:15:20	2026-09-04 08:15:20
27	1	S         	6	1	2026-09-04 08:15:20	2026-09-04 08:15:20
28	1	C         	6	3	2026-09-04 08:15:20	2026-09-04 08:15:20
29	1	D         	7	0.5	2026-09-04 08:15:20	2026-09-04 08:15:20
30	1	I         	7	5.3	2026-09-04 08:15:20	2026-09-04 08:15:20
31	1	S         	7	2.5	2026-09-04 08:15:20	2026-09-04 08:15:20
32	1	C         	7	5.3	2026-09-04 08:15:20	2026-09-04 08:15:20
33	1	D         	8	1	2026-09-04 08:15:20	2026-09-04 08:15:20
34	1	I         	8	5.7	2026-09-04 08:15:20	2026-09-04 08:15:20
35	1	S         	8	3	2026-09-04 08:15:20	2026-09-04 08:15:20
36	1	C         	8	5.7	2026-09-04 08:15:20	2026-09-04 08:15:20
37	1	D         	9	2	2026-09-04 08:15:20	2026-09-04 08:15:20
38	1	I         	9	6	2026-09-04 08:15:20	2026-09-04 08:15:20
39	1	S         	9	4	2026-09-04 08:15:20	2026-09-04 08:15:20
40	1	C         	9	6	2026-09-04 08:15:20	2026-09-04 08:15:20
41	1	D         	10	3	2026-09-04 08:15:20	2026-09-04 08:15:20
42	1	I         	10	6.5	2026-09-04 08:15:20	2026-09-04 08:15:20
43	1	S         	10	4.6	2026-09-04 08:15:20	2026-09-04 08:15:20
44	1	C         	10	6.3	2026-09-04 08:15:20	2026-09-04 08:15:20
45	1	D         	11	3.5	2026-09-04 08:15:20	2026-09-04 08:15:20
46	1	I         	11	7	2026-09-04 08:15:20	2026-09-04 08:15:20
47	1	S         	11	5	2026-09-04 08:15:20	2026-09-04 08:15:20
48	1	C         	11	6.5	2026-09-04 08:15:20	2026-09-04 08:15:20
49	1	D         	12	4	2026-09-04 08:15:20	2026-09-04 08:15:20
50	1	I         	12	7	2026-09-04 08:15:20	2026-09-04 08:15:20
51	1	S         	12	5.7	2026-09-04 08:15:20	2026-09-04 08:15:20
52	1	C         	12	6.7	2026-09-04 08:15:20	2026-09-04 08:15:20
53	1	D         	13	4.7	2026-09-04 08:15:20	2026-09-04 08:15:20
54	1	I         	13	7	2026-09-04 08:15:20	2026-09-04 08:15:20
55	1	S         	13	6	2026-09-04 08:15:20	2026-09-04 08:15:20
56	1	C         	13	7	2026-09-04 08:15:20	2026-09-04 08:15:20
57	1	D         	14	5.3	2026-09-04 08:15:20	2026-09-04 08:15:20
58	1	I         	14	7	2026-09-04 08:15:20	2026-09-04 08:15:20
59	1	S         	14	6.5	2026-09-04 08:15:20	2026-09-04 08:15:20
60	1	C         	14	7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
61	1	D         	15	6.5	2026-09-04 08:15:20	2026-09-04 08:15:20
62	1	I         	15	7	2026-09-04 08:15:20	2026-09-04 08:15:20
63	1	S         	15	6.5	2026-09-04 08:15:20	2026-09-04 08:15:20
64	1	C         	15	7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
65	1	D         	16	7	2026-09-04 08:15:20	2026-09-04 08:15:20
66	1	I         	16	7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
67	1	S         	16	7	2026-09-04 08:15:20	2026-09-04 08:15:20
68	1	C         	16	7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
69	1	D         	17	7	2026-09-04 08:15:20	2026-09-04 08:15:20
70	1	I         	17	7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
71	1	S         	17	7	2026-09-04 08:15:20	2026-09-04 08:15:20
72	1	C         	17	7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
73	1	D         	18	7	2026-09-04 08:15:20	2026-09-04 08:15:20
74	1	I         	18	7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
75	1	S         	18	7	2026-09-04 08:15:20	2026-09-04 08:15:20
76	1	C         	18	8	2026-09-04 08:15:20	2026-09-04 08:15:20
77	1	D         	19	7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
78	1	I         	19	7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
79	1	S         	19	7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
80	1	C         	19	8	2026-09-04 08:15:20	2026-09-04 08:15:20
81	1	D         	20	7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
82	1	I         	20	8	2026-09-04 08:15:20	2026-09-04 08:15:20
83	1	S         	20	7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
84	1	C         	20	8	2026-09-04 08:15:20	2026-09-04 08:15:20
85	1	D         	21	7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
86	1	I         	21	8	2026-09-04 08:15:20	2026-09-04 08:15:20
87	1	S         	21	7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
88	1	C         	21	8	2026-09-04 08:15:20	2026-09-04 08:15:20
89	1	D         	22	7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
90	1	I         	22	8	2026-09-04 08:15:20	2026-09-04 08:15:20
91	1	S         	22	7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
92	1	C         	22	8	2026-09-04 08:15:20	2026-09-04 08:15:20
93	1	D         	23	7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
94	1	I         	23	8	2026-09-04 08:15:20	2026-09-04 08:15:20
95	1	S         	23	7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
96	1	C         	23	8	2026-09-04 08:15:20	2026-09-04 08:15:20
97	1	D         	24	7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
98	1	I         	24	8	2026-09-04 08:15:20	2026-09-04 08:15:20
99	1	S         	24	7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
100	1	C         	24	8	2026-09-04 08:15:20	2026-09-04 08:15:20
101	2	D         	0	7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
102	2	I         	0	7	2026-09-04 08:15:20	2026-09-04 08:15:20
103	2	S         	0	7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
104	2	C         	0	7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
105	2	D         	1	6.5	2026-09-04 08:15:20	2026-09-04 08:15:20
106	2	I         	1	6	2026-09-04 08:15:20	2026-09-04 08:15:20
107	2	S         	1	7	2026-09-04 08:15:20	2026-09-04 08:15:20
108	2	C         	1	7	2026-09-04 08:15:20	2026-09-04 08:15:20
109	2	D         	2	4.3	2026-09-04 08:15:20	2026-09-04 08:15:20
110	2	I         	2	4	2026-09-04 08:15:20	2026-09-04 08:15:20
111	2	S         	2	6	2026-09-04 08:15:20	2026-09-04 08:15:20
112	2	C         	2	5.6	2026-09-04 08:15:20	2026-09-04 08:15:20
113	2	D         	3	2.5	2026-09-04 08:15:20	2026-09-04 08:15:20
114	2	I         	3	2.5	2026-09-04 08:15:20	2026-09-04 08:15:20
115	2	S         	3	4	2026-09-04 08:15:20	2026-09-04 08:15:20
116	2	C         	3	4	2026-09-04 08:15:20	2026-09-04 08:15:20
117	2	D         	4	1.5	2026-09-04 08:15:20	2026-09-04 08:15:20
118	2	I         	4	0.5	2026-09-04 08:15:20	2026-09-04 08:15:20
119	2	S         	4	2.5	2026-09-04 08:15:20	2026-09-04 08:15:20
120	2	C         	4	2.5	2026-09-04 08:15:20	2026-09-04 08:15:20
121	2	D         	5	0.5	2026-09-04 08:15:20	2026-09-04 08:15:20
122	2	I         	5	0	2026-09-04 08:15:20	2026-09-04 08:15:20
123	2	S         	5	1.5	2026-09-04 08:15:20	2026-09-04 08:15:20
124	2	C         	5	1.5	2026-09-04 08:15:20	2026-09-04 08:15:20
125	2	D         	6	0	2026-09-04 08:15:20	2026-09-04 08:15:20
126	2	I         	6	-2	2026-09-04 08:15:20	2026-09-04 08:15:20
127	2	S         	6	0.5	2026-09-04 08:15:20	2026-09-04 08:15:20
128	2	C         	6	0.5	2026-09-04 08:15:20	2026-09-04 08:15:20
129	2	D         	7	-1.3	2026-09-04 08:15:20	2026-09-04 08:15:20
130	2	I         	7	-3.5	2026-09-04 08:15:20	2026-09-04 08:15:20
131	2	S         	7	-1.3	2026-09-04 08:15:20	2026-09-04 08:15:20
132	2	C         	7	0	2026-09-04 08:15:20	2026-09-04 08:15:20
133	2	D         	8	-1.5	2026-09-04 08:15:20	2026-09-04 08:15:20
134	2	I         	8	-4.3	2026-09-04 08:15:20	2026-09-04 08:15:20
135	2	S         	8	-2	2026-09-04 08:15:20	2026-09-04 08:15:20
136	2	C         	8	-1.3	2026-09-04 08:15:20	2026-09-04 08:15:20
137	2	D         	9	-2.5	2026-09-04 08:15:20	2026-09-04 08:15:20
138	2	I         	9	-5.3	2026-09-04 08:15:20	2026-09-04 08:15:20
139	2	S         	9	-3	2026-09-04 08:15:20	2026-09-04 08:15:20
140	2	C         	9	-2.5	2026-09-04 08:15:20	2026-09-04 08:15:20
141	2	D         	10	-3	2026-09-04 08:15:20	2026-09-04 08:15:20
142	2	I         	10	-6	2026-09-04 08:15:20	2026-09-04 08:15:20
143	2	S         	10	-4.3	2026-09-04 08:15:20	2026-09-04 08:15:20
144	2	C         	10	-3.5	2026-09-04 08:15:20	2026-09-04 08:15:20
145	2	D         	11	-3.5	2026-09-04 08:15:20	2026-09-04 08:15:20
146	2	I         	11	-6.5	2026-09-04 08:15:20	2026-09-04 08:15:20
147	2	S         	11	-5.3	2026-09-04 08:15:20	2026-09-04 08:15:20
148	2	C         	11	-5.3	2026-09-04 08:15:20	2026-09-04 08:15:20
149	2	D         	12	-4.3	2026-09-04 08:15:20	2026-09-04 08:15:20
150	2	I         	12	-7	2026-09-04 08:15:20	2026-09-04 08:15:20
151	2	S         	12	-6	2026-09-04 08:15:20	2026-09-04 08:15:20
152	2	C         	12	-5.7	2026-09-04 08:15:20	2026-09-04 08:15:20
153	2	D         	13	-5.3	2026-09-04 08:15:20	2026-09-04 08:15:20
154	2	I         	13	-7.2	2026-09-04 08:15:20	2026-09-04 08:15:20
155	2	S         	13	-6.5	2026-09-04 08:15:20	2026-09-04 08:15:20
156	2	C         	13	-6	2026-09-04 08:15:20	2026-09-04 08:15:20
157	2	D         	14	-5.7	2026-09-04 08:15:20	2026-09-04 08:15:20
158	2	I         	14	-7.2	2026-09-04 08:15:20	2026-09-04 08:15:20
159	2	S         	14	-6.7	2026-09-04 08:15:20	2026-09-04 08:15:20
160	2	C         	14	-6.5	2026-09-04 08:15:20	2026-09-04 08:15:20
161	2	D         	15	-6	2026-09-04 08:15:20	2026-09-04 08:15:20
162	2	I         	15	-7.2	2026-09-04 08:15:20	2026-09-04 08:15:20
163	2	S         	15	-6.7	2026-09-04 08:15:20	2026-09-04 08:15:20
164	2	C         	15	-7	2026-09-04 08:15:20	2026-09-04 08:15:20
165	2	D         	16	-6.5	2026-09-04 08:15:20	2026-09-04 08:15:20
166	2	I         	16	-7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
167	2	S         	16	-7	2026-09-04 08:15:20	2026-09-04 08:15:20
168	2	C         	16	-7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
169	2	D         	17	6.7	2026-09-04 08:15:20	2026-09-04 08:15:20
170	2	I         	17	-7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
171	2	S         	17	-7.2	2026-09-04 08:15:20	2026-09-04 08:15:20
172	2	C         	17	-7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
173	2	D         	18	7	2026-09-04 08:15:20	2026-09-04 08:15:20
174	2	I         	18	-7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
175	2	S         	18	-7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
176	2	C         	18	-7.7	2026-09-04 08:15:20	2026-09-04 08:15:20
177	2	D         	19	-7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
178	2	I         	19	-7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
179	2	S         	19	-7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
180	2	C         	19	-7.9	2026-09-04 08:15:20	2026-09-04 08:15:20
181	2	D         	20	-7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
182	2	I         	20	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
183	2	S         	20	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
184	2	C         	20	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
185	2	D         	21	-7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
186	2	I         	21	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
187	2	S         	21	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
188	2	C         	21	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
189	2	D         	22	-7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
190	2	I         	22	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
191	2	S         	22	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
192	2	C         	22	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
193	2	D         	23	-7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
194	2	I         	23	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
195	2	S         	23	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
196	2	C         	23	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
197	2	D         	24	-7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
198	2	I         	24	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
199	2	S         	24	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
200	2	C         	24	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
201	3	D         	-24	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
202	3	I         	-24	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
203	3	S         	-24	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
204	3	C         	-24	-7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
205	3	D         	-23	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
206	3	I         	-23	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
207	3	S         	-23	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
208	3	C         	-23	-7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
209	3	D         	-22	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
210	3	I         	-22	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
211	3	S         	-22	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
212	3	C         	-22	-7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
213	3	D         	-21	-7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
214	3	I         	-21	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
215	3	S         	-21	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
216	3	C         	-21	-7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
217	3	D         	-20	-7	2026-09-04 08:15:20	2026-09-04 08:15:20
218	3	I         	-20	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
219	3	S         	-20	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
220	3	C         	-20	-7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
221	3	D         	-19	-6.8	2026-09-04 08:15:20	2026-09-04 08:15:20
222	3	I         	-19	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
223	3	S         	-19	-8	2026-09-04 08:15:20	2026-09-04 08:15:20
224	3	C         	-19	-7	2026-09-04 08:15:20	2026-09-04 08:15:20
225	3	D         	-18	-6.75	2026-09-04 08:15:20	2026-09-04 08:15:20
226	3	I         	-18	-7	2026-09-04 08:15:20	2026-09-04 08:15:20
227	3	S         	-18	-7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
228	3	C         	-18	-6.7	2026-09-04 08:15:20	2026-09-04 08:15:20
229	3	D         	-17	-6.7	2026-09-04 08:15:20	2026-09-04 08:15:20
230	3	I         	-17	-6.7	2026-09-04 08:15:20	2026-09-04 08:15:20
231	3	S         	-17	-7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
232	3	C         	-17	-6.7	2026-09-04 08:15:20	2026-09-04 08:15:20
233	3	D         	-16	-6.5	2026-09-04 08:15:20	2026-09-04 08:15:20
234	3	I         	-16	-6.7	2026-09-04 08:15:20	2026-09-04 08:15:20
235	3	S         	-16	-7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
236	3	C         	-16	-6.7	2026-09-04 08:15:20	2026-09-04 08:15:20
237	3	D         	-15	-6.3	2026-09-04 08:15:20	2026-09-04 08:15:20
238	3	I         	-15	-6.7	2026-09-04 08:15:20	2026-09-04 08:15:20
239	3	S         	-15	-7	2026-09-04 08:15:20	2026-09-04 08:15:20
240	3	C         	-15	-6.5	2026-09-04 08:15:20	2026-09-04 08:15:20
241	3	D         	-14	-6.1	2026-09-04 08:15:20	2026-09-04 08:15:20
242	3	I         	-14	-6.7	2026-09-04 08:15:20	2026-09-04 08:15:20
243	3	S         	-14	-6.5	2026-09-04 08:15:20	2026-09-04 08:15:20
244	3	C         	-14	-6.3	2026-09-04 08:15:20	2026-09-04 08:15:20
245	3	D         	-13	-5.9	2026-09-04 08:15:20	2026-09-04 08:15:20
246	3	I         	-13	-6.7	2026-09-04 08:15:20	2026-09-04 08:15:20
247	3	S         	-13	-6.5	2026-09-04 08:15:20	2026-09-04 08:15:20
248	3	C         	-13	-6	2026-09-04 08:15:20	2026-09-04 08:15:20
249	3	D         	-12	-5.7	2026-09-04 08:15:20	2026-09-04 08:15:20
250	3	I         	-12	-6.7	2026-09-04 08:15:20	2026-09-04 08:15:20
251	3	S         	-12	-6.5	2026-09-04 08:15:20	2026-09-04 08:15:20
252	3	C         	-12	-5.85	2026-09-04 08:15:20	2026-09-04 08:15:20
253	3	D         	-11	-5.3	2026-09-04 08:15:20	2026-09-04 08:15:20
254	3	I         	-11	-6.7	2026-09-04 08:15:20	2026-09-04 08:15:20
255	3	S         	-11	-6.5	2026-09-04 08:15:20	2026-09-04 08:15:20
256	3	C         	-11	-5.85	2026-09-04 08:15:20	2026-09-04 08:15:20
257	3	D         	-10	-4.3	2026-09-04 08:15:20	2026-09-04 08:15:20
258	3	I         	-10	-6.5	2026-09-04 08:15:20	2026-09-04 08:15:20
259	3	S         	-10	-6	2026-09-04 08:15:20	2026-09-04 08:15:20
260	3	C         	-10	-5.7	2026-09-04 08:15:20	2026-09-04 08:15:20
261	3	D         	-9	-3.5	2026-09-04 08:15:20	2026-09-04 08:15:20
262	3	I         	-9	-6	2026-09-04 08:15:20	2026-09-04 08:15:20
263	3	S         	-9	-4.7	2026-09-04 08:15:20	2026-09-04 08:15:20
264	3	C         	-9	-4.7	2026-09-04 08:15:20	2026-09-04 08:15:20
265	3	D         	-8	-3.25	2026-09-04 08:15:20	2026-09-04 08:15:20
266	3	I         	-8	-5.7	2026-09-04 08:15:20	2026-09-04 08:15:20
267	3	S         	-8	-4.3	2026-09-04 08:15:20	2026-09-04 08:15:20
268	3	C         	-8	-4.3	2026-09-04 08:15:20	2026-09-04 08:15:20
269	3	D         	-7	-3	2026-09-04 08:15:20	2026-09-04 08:15:20
270	3	I         	-7	-4.7	2026-09-04 08:15:20	2026-09-04 08:15:20
271	3	S         	-7	-3.5	2026-09-04 08:15:20	2026-09-04 08:15:20
272	3	C         	-7	-3.5	2026-09-04 08:15:20	2026-09-04 08:15:20
273	3	D         	-6	-2.75	2026-09-04 08:15:20	2026-09-04 08:15:20
274	3	I         	-6	-4.3	2026-09-04 08:15:20	2026-09-04 08:15:20
275	3	S         	-6	-3	2026-09-04 08:15:20	2026-09-04 08:15:20
276	3	C         	-6	-3	2026-09-04 08:15:20	2026-09-04 08:15:20
277	3	D         	-5	-2.5	2026-09-04 08:15:20	2026-09-04 08:15:20
278	3	I         	-5	-3.5	2026-09-04 08:15:20	2026-09-04 08:15:20
279	3	S         	-5	-2	2026-09-04 08:15:20	2026-09-04 08:15:20
280	3	C         	-5	-2.5	2026-09-04 08:15:20	2026-09-04 08:15:20
281	3	D         	-4	-1.5	2026-09-04 08:15:20	2026-09-04 08:15:20
282	3	I         	-4	-3	2026-09-04 08:15:20	2026-09-04 08:15:20
283	3	S         	-4	-1.5	2026-09-04 08:15:20	2026-09-04 08:15:20
284	3	C         	-4	-0.5	2026-09-04 08:15:20	2026-09-04 08:15:20
285	3	D         	-3	-1	2026-09-04 08:15:20	2026-09-04 08:15:20
286	3	I         	-3	-2	2026-09-04 08:15:20	2026-09-04 08:15:20
287	3	S         	-3	-1	2026-09-04 08:15:20	2026-09-04 08:15:20
288	3	C         	-3	0	2026-09-04 08:15:20	2026-09-04 08:15:20
289	3	D         	-2	-0.5	2026-09-04 08:15:20	2026-09-04 08:15:20
290	3	I         	-2	-1.5	2026-09-04 08:15:20	2026-09-04 08:15:20
291	3	S         	-2	-0.5	2026-09-04 08:15:20	2026-09-04 08:15:20
292	3	C         	-2	0.3	2026-09-04 08:15:20	2026-09-04 08:15:20
293	3	D         	-1	-0.25	2026-09-04 08:15:20	2026-09-04 08:15:20
294	3	I         	-1	0	2026-09-04 08:15:20	2026-09-04 08:15:20
295	3	S         	-1	0	2026-09-04 08:15:20	2026-09-04 08:15:20
296	3	C         	-1	0.5	2026-09-04 08:15:20	2026-09-04 08:15:20
297	3	D         	0	0	2026-09-04 08:15:20	2026-09-04 08:15:20
298	3	I         	0	0.5	2026-09-04 08:15:20	2026-09-04 08:15:20
299	3	S         	0	1	2026-09-04 08:15:20	2026-09-04 08:15:20
300	3	C         	0	1.5	2026-09-04 08:15:20	2026-09-04 08:15:20
301	3	D         	1	0.5	2026-09-04 08:15:20	2026-09-04 08:15:20
302	3	I         	1	1	2026-09-04 08:15:20	2026-09-04 08:15:20
303	3	S         	1	1.5	2026-09-04 08:15:20	2026-09-04 08:15:20
304	3	C         	1	3	2026-09-04 08:15:20	2026-09-04 08:15:20
305	3	D         	2	0.7	2026-09-04 08:15:20	2026-09-04 08:15:20
306	3	I         	2	1.5	2026-09-04 08:15:20	2026-09-04 08:15:20
307	3	S         	2	2	2026-09-04 08:15:20	2026-09-04 08:15:20
308	3	C         	2	4	2026-09-04 08:15:20	2026-09-04 08:15:20
309	3	D         	3	1	2026-09-04 08:15:20	2026-09-04 08:15:20
310	3	I         	3	3	2026-09-04 08:15:20	2026-09-04 08:15:20
311	3	S         	3	3	2026-09-04 08:15:20	2026-09-04 08:15:20
312	3	C         	3	4.3	2026-09-04 08:15:20	2026-09-04 08:15:20
313	3	D         	4	1.3	2026-09-04 08:15:20	2026-09-04 08:15:20
314	3	I         	4	4	2026-09-04 08:15:20	2026-09-04 08:15:20
315	3	S         	4	3.5	2026-09-04 08:15:20	2026-09-04 08:15:20
316	3	C         	4	5.5	2026-09-04 08:15:20	2026-09-04 08:15:20
317	3	D         	5	1.5	2026-09-04 08:15:20	2026-09-04 08:15:20
318	3	I         	5	4.3	2026-09-04 08:15:20	2026-09-04 08:15:20
319	3	S         	5	4	2026-09-04 08:15:20	2026-09-04 08:15:20
320	3	C         	5	5.7	2026-09-04 08:15:20	2026-09-04 08:15:20
321	3	D         	6	2	2026-09-04 08:15:20	2026-09-04 08:15:20
322	3	I         	6	5	2026-09-04 08:15:20	2026-09-04 08:15:20
323	3	S         	6	0	2026-09-04 08:15:20	2026-09-04 08:15:20
324	3	C         	6	6	2026-09-04 08:15:20	2026-09-04 08:15:20
325	3	D         	7	2.5	2026-09-04 08:15:20	2026-09-04 08:15:20
326	3	I         	7	5.5	2026-09-04 08:15:20	2026-09-04 08:15:20
327	3	S         	7	4.7	2026-09-04 08:15:20	2026-09-04 08:15:20
328	3	C         	7	6.3	2026-09-04 08:15:20	2026-09-04 08:15:20
329	3	D         	8	3.5	2026-09-04 08:15:20	2026-09-04 08:15:20
330	3	I         	8	6.5	2026-09-04 08:15:20	2026-09-04 08:15:20
331	3	S         	8	5	2026-09-04 08:15:20	2026-09-04 08:15:20
332	3	C         	8	6.5	2026-09-04 08:15:20	2026-09-04 08:15:20
333	3	D         	9	4	2026-09-04 08:15:20	2026-09-04 08:15:20
334	3	I         	9	6.7	2026-09-04 08:15:20	2026-09-04 08:15:20
335	3	S         	9	5.5	2026-09-04 08:15:20	2026-09-04 08:15:20
336	3	C         	9	6.7	2026-09-04 08:15:20	2026-09-04 08:15:20
337	3	D         	10	4.7	2026-09-04 08:15:20	2026-09-04 08:15:20
338	3	I         	10	7	2026-09-04 08:15:20	2026-09-04 08:15:20
339	3	S         	10	6	2026-09-04 08:15:20	2026-09-04 08:15:20
340	3	C         	10	7	2026-09-04 08:15:20	2026-09-04 08:15:20
341	3	D         	11	4.85	2026-09-04 08:15:20	2026-09-04 08:15:20
342	3	I         	11	7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
343	3	S         	11	6.2	2026-09-04 08:15:20	2026-09-04 08:15:20
344	3	C         	11	7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
345	3	D         	12	5	2026-09-04 08:15:20	2026-09-04 08:15:20
346	3	I         	12	7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
347	3	S         	12	6.3	2026-09-04 08:15:20	2026-09-04 08:15:20
348	3	C         	12	7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
349	3	D         	13	5.5	2026-09-04 08:15:20	2026-09-04 08:15:20
350	3	I         	13	7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
351	3	S         	13	6.5	2026-09-04 08:15:20	2026-09-04 08:15:20
352	3	C         	13	7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
353	3	D         	14	6	2026-09-04 08:15:20	2026-09-04 08:15:20
354	3	I         	14	7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
355	3	S         	14	6.7	2026-09-04 08:15:20	2026-09-04 08:15:20
356	3	C         	14	7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
357	3	D         	15	6.3	2026-09-04 08:15:20	2026-09-04 08:15:20
358	3	I         	15	7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
359	3	S         	15	7	2026-09-04 08:15:20	2026-09-04 08:15:20
360	3	C         	15	7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
361	3	D         	16	6.5	2026-09-04 08:15:20	2026-09-04 08:15:20
362	3	I         	16	7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
363	3	S         	16	7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
364	3	C         	16	7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
365	3	D         	17	6.7	2026-09-04 08:15:20	2026-09-04 08:15:20
366	3	I         	17	7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
367	3	S         	17	7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
368	3	C         	17	7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
369	3	D         	18	7	2026-09-04 08:15:20	2026-09-04 08:15:20
370	3	I         	18	7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
371	3	S         	18	7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
372	3	C         	18	8	2026-09-04 08:15:20	2026-09-04 08:15:20
373	3	D         	19	7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
374	3	I         	19	8	2026-09-04 08:15:20	2026-09-04 08:15:20
375	3	S         	19	7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
376	3	C         	19	8	2026-09-04 08:15:20	2026-09-04 08:15:20
377	3	D         	20	7.3	2026-09-04 08:15:20	2026-09-04 08:15:20
378	3	I         	20	8	2026-09-04 08:15:20	2026-09-04 08:15:20
379	3	S         	20	7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
380	3	C         	20	8	2026-09-04 08:15:20	2026-09-04 08:15:20
381	3	D         	21	7.5	2026-09-04 08:15:20	2026-09-04 08:15:20
382	3	I         	21	8	2026-09-04 08:15:20	2026-09-04 08:15:20
383	3	S         	21	8	2026-09-04 08:15:20	2026-09-04 08:15:20
384	3	C         	21	8	2026-09-04 08:15:20	2026-09-04 08:15:20
385	3	D         	22	8	2026-09-04 08:15:20	2026-09-04 08:15:20
386	3	I         	22	8	2026-09-04 08:15:20	2026-09-04 08:15:20
387	3	S         	22	8	2026-09-04 08:15:20	2026-09-04 08:15:20
388	3	C         	22	8	2026-09-04 08:15:20	2026-09-04 08:15:20
389	3	D         	23	8	2026-09-04 08:15:20	2026-09-04 08:15:20
390	3	I         	23	8	2026-09-04 08:15:20	2026-09-04 08:15:20
391	3	S         	23	8	2026-09-04 08:15:20	2026-09-04 08:15:20
392	3	C         	23	8	2026-09-04 08:15:20	2026-09-04 08:15:20
393	3	D         	24	8	2026-09-04 08:15:20	2026-09-04 08:15:20
394	3	I         	24	8	2026-09-04 08:15:20	2026-09-04 08:15:20
395	3	S         	24	8	2026-09-04 08:15:20	2026-09-04 08:15:20
396	3	C         	24	8	2026-09-04 08:15:20	2026-09-04 08:15:20
\.


--
-- Data for Name: disc_profiles; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.disc_profiles (id, pattern_code, title, general_description, created_at, updated_at, suitable_jobs) FROM stdin;
1	C	LOGICAL THINKER	Seorang yang praktis, cakap dan unik. Ia orang yang mampu menilai diri sendiri dan kritis terhadap dirinya dan orang lain. Ia menyukai hal yang detil dan logis; secara alamiah ia sangat analitis. Karena menyimpan informasi, ia meneliti isu berulang-ulang kali. Ia cenderung malu dan tertutup; ia hati-hati dalam membuat keputusan yang berdasarkan pada logika, bukan emosi, selalu menggunakan pertanyaan \\"bagaimana dan mengapa\\". Ia mengerjakan sesuatu dengan sistematis dan akurat. Ia rapi dan terorganisir sebab ia merasa bahwa keadaan berantakan sama dengan mutu yang rendah; demikian juga, rapi dan teratur merupakan mutu yang tinggi. Sangat teliti dalam segala sesuatu seperti halnya dalam pekerjaan dan penggunaan waktunya. Ia merencanakan dan mengorganisir semua sisi kehidupannya. Kelambanan sangat mengganggunya dan tak dapat ditolerir.	2026-09-04 08:15:20	2026-09-04 08:15:20	Planner (any function), Engineer (Installation, Technical), Technical/Research (Chemist Technician), Academic, Statistician, Government Worker, IT Management, Prison Officer, Quality Controller.
2	D	ESTABLISHER	Memiliki rasa ego yang tinggi dan cenderung invidualis dengan standard yang sangat tinggi. Ia lebih suka menganalisa masalah sendirian daripada bersama orang lain. Rasa egoisnya yang kuat membuatnya tidak nyaman di bawah kendali orang lain; ia lebih suka menjadi \\"boss\\" dan menetapkan standard tinggi baik untuk dirinya maupun orang lain. Ia menghindari sesuatu yang biasa-biasa dan cenderung mencari tantangan yang baru. Ia menyukai petualangan dan kadang-kadang beralih ke dalam petualangan baru sebelum mempertimbangkannya secara menyeluruh. Mampu memimpin situasi dan orang lain dalam rangka mencapai sasarannya; ia ingin selalu unggul dalam persaingan dengan taruhan apapun.	2026-09-04 08:15:20	2026-09-04 08:15:20	Attorney, Researcher, Sales Representative, Planning Consultant, Transport Personnel, Production (Director, Manager, Supervisor), Technologist, Strategic Planning, Trouble Shooting, Marketing Services, Consultant, Engineering (Director, Manager, Supervisor) and Self-Employment.
3	D / C-D	DESIGNER	Seorang yang sangat berorientasi pada tugas dan sensitif pada permasalahan. Ia lebih mempedulikan tugas yang ada dibanding orang-orang di sekitarnya, termasuk perasaan mereka. Sangat kukuh/keras dan mempunyai pendekatan yang efektif dalam pemecahan masalah. Oleh karena sifat alamiah dan keinginannya akan hasil yang terukur, Akan tampak dingin, tidak berperasaan dan menjaga jarak. Ia membuat keputusan berdasar pada fakta, bukan emosi. Cenderung pendiam dan tidak mudah percaya.	2026-09-04 08:15:20	2026-09-04 08:15:20	Engineering (Management, Research, Design), Research (R&D), Planning, Chemist, Accountancy, Specialist, Finance, Technician, Quality Control, Production Planning/Management, Design Engineer, Bookkeeper, Chemist Technician, Safety Officer, Librarian.
4	D / I-D	NEGOTIATOR	Merupakan seorang pemimpin integratif yang bekerja dengan dan melalui orang lain.  Ia ramah, memiliki perhatian yang tinggi akan orang dan juga mempunyai kemampuan untuk memperoleh hormat dan penghargaan dari berbagai tipe orang.  Melakukan pekerjaannya dengan cara yang bersahabat, baik dalam mencapai sasarannya maupun meyakinkan pandangannya kepada orang lain.  Ia tidak begitu memperhatikan hal-hal kecil.  Kadang bertindak sesuai dengan kata hati/impulsif, terlalu antusias dan sangat banyak bicara.  Ia terlalu berlebihan menilai kemampuannya dalam memotivasi atau mengubah perilaku orang lain.  Mencari kebebasan dari rutinitas, menginginkan otoritas/wewenang dan juga prestise.  Ia menginginkan aktivitas yang bervariasi dan bekerja lebih efisien jika data-data analitis disediakan oleh orang lain.  Menginginkan penugasan yang mengutamakan mobilitas dan tantangan.	2026-09-04 08:15:20	2026-09-04 08:15:20	Sales and Marketing (Directing, Manager, Person), Public Relations, Recruitment Consultant, Politician, Director, Self-Employed, Hotelier, Travel Agent, Trainer, Hospitality, Lawyer, Solicitor, Motivators, Team Leader, Politician, Trainer, Lecturer, Theatrical Agent, General Management and Leading People, Attorney.
5	D / I-D-C	CONFIDENT & DETERMINED	Sangat berorientasi terhadap tugas dan juga menyukai orang.  Ia sangat baik dalam menarik orang/recruiting.  Seorang yang bersahabat, tetapi menyukai keadaan di mana tugas-tugas harus dilakukan dengan benar.  Ia kadang-kadang tampak dingin dan mendominasi.  Ia juga bisa sangat fokus pada tugas dan melupakan orang-orang di sekitarnya.  Sangat mengharapkan orang-orang terlibat dalam proyeknya, tetapi tidak memperdulikan apa yang diinginkan oleh orang-orang itu.  Ia perlu mendengar dan memikirkan  apa yang menjadi keinginan orang di sekitarnya, khususnya kesempatan untuk mencoba.  Ia sangat membutuhkan persetujuan sosial seperti halnya ia sangat mempercayai orang lain.  Karena itu, ia kadang-kadang berlebihan dalam menilai orang dan kemampuannya.  Ia tampak tidak konsisten dan tidak karuan karena ketidakmampuannya berkonsentrasi dan fokus dalam waktu yang lama.  Perlu belajar untuk secara sungguh-sungguh mendengarkan orang-orang di sekitarnya dari pada selalu berpikir apa yang ingin dikatakan.  Ia mempunyai kemampuan logika yang tinggi ketika ia mau menggunakannya.	2026-09-04 08:15:20	2026-09-04 08:15:20	Specialist/Technical Selling (Computer, Finance, Engineer and others, Chef, Technical/Capital Equipment Selling), Financial (Manager, Specialist), Computer Hardware Sales, Engineering (Manager, Designer, Buyer, Draughtsman), Project Engineer, Sales Engineer, Consultant, Trainer, Lecturer, Hotelier, Insurance, Mortgage and Finance Sales, Teacher, Travel Agent, Personnel and Marketing Services.
6	D / I-D-S	REFORMER	Seorang yang bersahabat dan sosial; ia juga suka mengendalikan situasi dan menjadi pemimpin.  Ia menyelesaikan tugasnya melalui keterampilan sosialnya; ia peduli dan menerima orang lain.  Ia berkonsentrasi pada tugas yang ada di tangannya sampai selesai dan akan minta bantuan orang lain jika perlu.  Ia menyadari keterbatasannya dan meminta bantuan jika memerlukannya.  Ia disukai dan orang ingin menolongnya.  Senang membagi kebanggaannya dengan kelompok; ia seorang team player tetapi juga team leader.  Menginginkan popularitas dan pengakuan.	2026-09-04 08:15:20	2026-09-04 08:15:20	Hotelier, Customer Service, Complaints Manager, Recruiting Agent, Sales (Manager/Person), Marketing Services, Public Relations, Politician, Computer Software Sales, Lecturer, Engineering and Production (Manager/Supervisor).
7	D / I-S-D	MOTIVATOR	Seorang yang menampilkan gaya bersemangat ketika termotivasi pada sasaran.  Ia lebih suka memimpin atau melibatkan diri, walaupun ia juga mau melayani sebagai pembantu.  Ia membutuhkan pengakuan dan penghargaan serta senang pada peran pendukung.  Ia peduli kepada orang-orang di sekitarnya dan akan mempertimbangkan perasaan orang lain dalam proses pengambilan keputusan.  Menampilkan keterampilan berhubungan dan berkomunikasi dengan sangat baik.  Ia akan berusaha keras menyelesaikan tugas dengan cepat dan efisien.	2026-09-04 08:15:20	2026-09-04 08:15:20	Hotelier, Community Counseling, Customer Service, Complaints Manager, Community Work, Recruitment Consultant, Hospitality, Teacher, Telemarketing, Production Manager, Complaints Manager, Recruiting Agent, Sales (Manager/Person), Marketing Services, Public Relations, Politician, Call Centre Manager, Lecturer, Engineering and Production (Manager/Supervisor).
8	D / S-D-C / S-C-D	INQUIRER	Seorang yang sabar, terkontrol dan suka menggali fakta dan jalan keluar.  Ia tenang dan ramah.  Ia merencanakan pekerjaan dengan hati-hati, tetapi agresif, menanyakan sesuatu serta mengumpulkan data pendukung.  Kemudian ia bekerja dengan konsisten dengan arahan yang benar.  Menjadi individu yang penuh perhatian, rendah hati, dan ia berhubungan baik dengan hampir semua orang.  Seorang yang konsisten dan suka menolong. People skill darinya melebihi orientasi tugasnya.	2026-09-04 08:15:20	2026-09-04 08:15:20	Directing, Managing or Supervising (in Engineering, Accountancy, Research and Development and Computing disciplines), Research Manager, Scientific Work, Accountant, Administration, Project Engineer, Draughtsman, Designer, Analyst, Finance, Chemist, Technical Service Support, Flight Attendant, Technician, Service Engineer, Service Manager, Security Specialist.
9	D-I	PENGAMBIL KEPUTUSAN	Tidak basa-basi dan tegas, ia cenderung merupakan seorang invidualis yang kuat. Ia berpandangan jauh ke depan, progresif dan mau berkompetisi untuk mencapai sasaran. DI seorang yang selalu ingin tahu dan mempunyai minat dengan cakupan yang luas. Ia seorang yang logis, kritis dan tajam dalam memecahkan masalah. Sering kali ia tampak imajinatif. Ia mempunyai kemampuan memimpinan yang baik. Ia kadang tampak keras kepala atau dingin karena orientasi dan prioritasnya pada tugas cenderung melebihi orientasi terhadap sesama. Ia mencanangkan standard tinggi pada dirinya dan akan sangat kritis ketika standard ini tidak dicapai. Ia juga menempatkan standard tinggi pada orang-orang di sekitarnya, serta mengutamakan kesempurnaan. Ia menginginkan otoritas yang jelas dan menyukai tugas-tugas baru.	2026-09-04 08:15:20	2026-09-04 08:15:20	General Management (Directing/Managing/Supervising, Public Relations, Business Management, Conflict Resolution, Industrial Relations, Business Consultant, Trouble Shooting, Sales and Sales Management, Marketing, Promoting, Production (Director, Manager, Supervisor), Consultancy, Publishing, Sales Executive, Promotional Work, Brokers, Self-Employment, Advertising, Lecturing, Dealing/Broking.
10	D-I-S	DIRECTOR	Fokus pada penyelesaian pekerjaan dan menunjukkan penghargaan yang tinggi kepada orang lain.  Ia memiliki kemampuan untuk menggerakkan orang dan pekerjaan dikarenakan keterampilannya berpikir ke depan dan hubungan antar manusia.  Tidak berorientasi detil, ia fokus pada target secara keseluruhan dengan menyerahkan hal detil kepada orang lain.  Enerjik dan sosial, ia mampu memotivasi orang lain sambil menyelesaikan pekerjaannya.  Ia menampilkan rasa percaya diri dan mampu meyakinkan orang lain.  Sekali ia memutuskan sesuatu, ia akan terus mengerjakannya dan bertahan sampai selesai.	2026-09-04 08:15:20	2026-09-04 08:15:20	Engineering and Production (Directing, Managing, Supervising), Sales, Sales Management, Service Manager, Distribution, Public Relations, Office Management, Account Manager, Customer Service, Retail Manager, IT, Lecturer, Logistics, Manager-General, National Accounts Manager, Teacher, Projects Manager.
11	D-S	SELF-MOTIVATED	Seorang yang obyektif dan analitis.  Ia ingin terlibat dalam situasi, dan ia juga ingin memberikan bantuan dan dukungan kepada orang yang ia hormati.  Secara internal termotivasi oleh target pribadi, ia berorientasi terhadap pekerjaannya tapi juga menyukai hubungan dengan sesama.  Karena determinasinya yang kuat, ia sering berhasil dalam berbagai hal; karakternya yang tenang, stabil dan daya tahannya yang tinggi memiliki kontribusi dalam keberhasilannya.  Ulet dalam memulai pekerjaan. Ia akan berusaha keras untuk mencapai sasarannya.  Seorang yang mandiri dan cermat serta memiliki tindak lanjut yang baik.	2026-09-04 08:15:20	2026-09-04 08:15:20	Engineering and Production (Directing, Managing, Supervising), Project Management, Researcher, Chemist (R&D), Planner, Engineering (R&D), Systems Analyst, Commercial Planner, Computer Engineer, Programmer, IT, Other computer-related disciplines, Technical Trouble Shooting and Directing, Lawyer, Solicitor, Development Engineer, Work Study, Barrister, Attorney.
12	I / C-I-S	MEDIATOR	Merupakan individu yang berorientasi pada orang, ia mampu menggabungkan ketepatan dan loyalitas.  Ia cenderung peka dan mempunyai standard yang tinggi.  Ia menginginkan stabilitas dan berorientasi terhadap sasaran.  Ia menginginkan pengakuan sosial dan perhatian pribadi.  Ia bersahabat, antusias, informal, banyak bicara, dan mungkin sangat mencemaskan apa yang dipikirkan oleh orang lain.  Ia menolak agresi, dan mengharapkan suasana harmonis.  Ia cenderung cukup cerdas dalam berbagai hal. Ia merupakan pencari fakta yang sangat baik dan akan membuat keputusan yang baik setelah mengumpulkan fakta dan data pendukung.	2026-09-04 08:15:20	2026-09-04 08:15:20	Engineering and Production (Supervisor, Installer, Technician, Service and Design), Research (Supervisor, Chemist, Lab. Technician), Trainer, Finance (Supervisor, Accountant, Advisor), Public Relations, Administration, Office Administrator, Market Analyst, System Analyst, Programmer, Selling (Technical/Service).
13	I / C-S-I	PRACTITIONER	Merupakan individu yang berorientasi pada orang, ia mampu menggabungkan ketepatan dan loyalitas.  Ia cenderung peka dan mempunyai standard yang tinggi.  Ia menginginkan stabilitas dan berorientasi terhadap sasaran.  Ia menginginkan pengakuan sosial dan perhatian pribadi.  Bersahabat, antusias, informal, banyak bicara, dan mungkin sangat mencemaskan apa yang dipikirkan oleh orang lain.  Ia menolak agresi dan mengharapkan suasana harmonis.  Ia cenderung cukup cerdas dalam berbagai hal. Ia merupakan pencari fakta yang sangat baik dan akan membuat keputusan yang baik setelah mengumpulkan fakta dan data pendukung.	2026-09-04 08:15:20	2026-09-04 08:15:20	Engineering and Production (Supervisor, Installer, Technician, Service and Design), Research (Supervisor, Chemist), Trainer, Finance (Manager, Supervisor, Accountant, Advisor), Public Relations-Administration, Purchasing, Chemist Research, Office Administrator, Computer Programmer, Market Analyst, System Analyst, Programmer, Research and Development Supervisor, Laboratory Technician, Legal, Selling (Technical/Service).
14	I-S-C / I-C-S	RESPONSIVE & THOUGHTFUL	Merupakan individu yang berorientasi pada orang dan lancar berkomunikasi serta loyal.  Ia cenderung sensitif dan mempunyai standard yang tinggi.  Keputusannya dibuat berdasarkan fakta dan data pendukung.  Ia sepertinya tidak bisa diam.  Ia perlu untuk lebih terus terang dan jangan terlalu subyektif.  Ia butuh pengakuan sosial dan perhatian pribadi; ia dapat cepat akrab dengan orang lain.  Ia bersahabat, antusias, informal, banyak bicara dan terlalu khawatir terhadap apa yang dipikirkan orang.  Ia menguasai banyak hal.  Ia ingin diterima sebagai anggota kelompok dan ingin mengetahui secara pasti apa yang diharapkan darinya sebelum ia memulai proyek baru.	2026-09-04 08:15:20	2026-09-04 08:15:20	Actors, Chef, Personnel, Welfare, Broadcasting, Training, Attorney, Teaching, Accounting, Technical Instructor, Accounting-General, Accounts Supervisor, Customer Services, Public Relations, Artist, Hotelier, Demonstrator, Florist/Floral Designer, Engineering (Sales, Service, Project, Draughtsman, Designer), Graphic Designer, Specialist (Soft/Services), Selling, Purchasing, Singers, Technical Instructor, Personnel Management, Politician, Supervising (Engineering, Production, Accounts), Administration Work, Sales Engineer, Secretarial, Industrial Relations Specialist.
15	S	SPECIALIST	Merupakan individu konsisten yang berusaha menjaga lingkungan/suasana yang tidak berubah.  Ia bekerja dengan baik bersama orang-orang dengan berbagai kepribadian karena perilakunya yang terkendali dan rendah hati.  Sabar, loyal dan suka menolong.  Persahabatan dikembangkannya dengan lambat dan selektif.  Ia tidak bosan dengan rutinitas dan sangat baik bekerja dengan petunjuk dan peraturan yang jelas. Ia mengharapkan bantuan dan supervisi pada saat mengawali proyek baru.  Ia butuh waktu untuk menyesuaikan diri dengan perubahan dan sungkan menjalankan \\"cara-cara lama mengerjakan sesuatu\\".  Ia akan menghindari konfrontasi dan berusaha sekuat tenaga memendam perasaannya.	2026-09-04 08:15:20	2026-09-04 08:15:20	Administrative Work, Engineering and Production areas (Sales, Services, Project, Painter, Plumber, Draughtsman, Designer, Operative), Chef, Accounting, Telemarketing/Tele-Sales, Research and Development, Administrator, Florist/Floral Designer, Retail-General, Sales-General, Accounting-General, Service-General, Landscape Gardener.
16	S / C-S	PERFECTIONIST	Berpikir sistematis dan cenderung mengikuti prosedur dalam kehidupan pribadi dan pekerjaannya.  Teratur dan memiliki perencanaan yang baik, ia teliti dan fokus pada detil.  Bertindak dengan penuh kebijaksanaan, diplomatis dan jarang menentang rekan kerjanya dengan sengaja.  Ia sangat berhati-hati, sungguh-sungguh mengharapkan akurasi dan standard tinggi dalam pekerjaannya.  Ia cenderung terjebak dalam hal detil, khususnya jika harus memutuskan.  Menginginkan adanya petunjuk standard pelaksanaan kerja dan tanpa perubahan mendadak.	2026-09-04 08:15:20	2026-09-04 08:15:20	Researcher (Technician, Chemist, Quality Control), Engineer (Project, Draughtsman, Armed Forces, Designer), Statistician, Surveyor, Optician, Medical Specialist, Health Care, IT Management, Planner, Technical Writing, Production, Dentist, Quality Control, Planning, Dental Technician, Accounting, Computer Programmer, Psychologist, Surgeon, Architect, Medical Specialist.
17	S-C	PEACEMAKER, RESPECTFULL & ACCURATE	Ia adalah orang yang baik secara alamiah dan sangat berorientasi detil.  Ia peduli dengan orang-orang di sekitarnya dan mempunyai kualitas yang membuatnya sangat teliti dalam penyelesaian tugas.  Ia mempertimbangkan sekelilingnya dengan hati-hati sebelum membuat keputusan untuk melihat pengaruhnya pada mereka; saat tertentu ia terlalu hati-hati.  Jika ia merasa seseorang memanfaatkan situasi, ia akan memperlambat kerjanya sehingga dapat mengamati apa yang sedang berlangsung di sekitarnya.	2026-09-04 08:15:20	2026-09-04 08:15:20	Office (Manager, Supervisor, Person), Chief Clerk, General Administrator, Production Supervisor, Planner, Accountant, Research and Development, Flight Attendant, Engineering (Project Manager, Supervisor, Technician), Computer Programmer, Draughtsman, Soft/Service Selling, Doctor, Cashier, Receptionist, Data Entry, Planner, Word Processing, Property Manager, Database Administrator, Health Care, Statistician, Nursing-Administration, Company Secretary, System Analyst, Programmer, Statistician, Accounting-General, Security Specialist.
18	D-C	CHALLENGER	Seorang yang sensitif terhadap permasalahan, dan memiliki kreativitas yang baik dalam memecahkan masalah. Ia dapat menyelesaikan tugas-tugas penting dalam waktu singkat karena mempunyai keputusan yang kuat. Seorang yang tekun dan memiliki reaksi yang cepat.  Ia akan meneliti dan mengejar semua kemungkinan yang ada dalam mencari solusi permasalahan.  Ia banyak memberikan ide-ide dengan berfokus pada pekerjaan. Usaha yang keras pada ketepatan akan mengimbangi keinginannya pada hasil yang terukur.  Ia cenderung perfeksionis dan dapat juga memperlambat pengambilan keputusan karena keinginannya untuk menentukan pilihan yang terbaik.	2026-09-04 08:15:20	2026-09-04 08:15:20	Engineering (Management, Research, Design), Actuaries, Research (R&D), Planning, Chemist, Hospital Supervisor, Industrial Marketing, Investment Banking, Medical Administrator, Mortgage Brokers, Accountancy, Fund Management, Specialist Finance, Quality Control and Specialist work in any area where knowledge and experience is available, Production, Financial Services, Technical Management, Project Leader, Matron, Strategic Planning, Industrial Marketing.
19	D-I-C	CHANCELLOR	Ia menggabungkan antara kesenangan dengan pekerjaan/bisnis ketika melakukan sesuatu. Ia kelihatan menyukai hubungan dengan sesama tetapi juga dapat mengerjakan hal-hal detil. Ia ingin melakukan segala sesuatu dengan tepat, dan ia akan menyelesaikan tugasnya untuk meyakinkan ketepatan dan kelengkapannya. Seorang yang ramah secara alami dan menikmati interaksi dengan sesama, akan tetapi ia akan juga menilai orang dan tugas secara hati-hati; persahabatannya akan bergeser sesuai dengan dorongan hatinya pada orang lain di sekitarnya. Ia sering melalaikan perencanaan yang seksama dan akan beralih ke pada proyek-proyek baru tanpa pertimbangan yang menyeluruh.	2026-09-04 08:15:20	2026-09-04 08:15:20	Technical/Scientific (Directing, Management, Supervision), Engineering, Finance, Production Planning, Personnel Disciplines, Self-Employment, Credit Manager, Planner, Fund Management, Computer Hardware/Software Sales, IT, Business Consultant, Banking, Logistics, Lecturing, Work Study, Film Director, Transport, Consultancy, Industrial Relations and Computers (Selling, Software, Systems Analyst) and General Manager.
20	D-S-I	DIRECTOR	Seorang yang obyektif dan analitis.  Ia ingin terlibat dalam situasi, dan ia juga ingin memberikan bantuan dan dukungan kepada orang yang ia hormati.  Secara internal termotivasi oleh target pribadi, ia berorientasi terhadap pekerjaannya tapi juga menyukai hubungan dengan sesama.  Karena determinasinya yang kuat, ia sering berhasil dalam berbagai hal; karakternya yang tenang, stabil dan daya tahannya yang tinggi memiliki kontribusi dalam keberhasilannya.  Ulet dalam memulai pekerjaan. Ia akan berusaha keras untuk mencapai sasarannya.  Seorang yang mandiri dan cermat serta memiliki tindak lanjut yang baik.	2026-09-04 08:15:20	2026-09-04 08:15:20	Engineering and Production (Directing, Managing, Supervising), Sales, Sales Management, Service Manager, Distribution, Public Relations, Creative Designer, Office Management, Chief Engineer, Business Consultant, Chief Financial Officer, Customer Service, National Accounts Manager, Chief Accountant, Lecturer, Projects Manager, Research Planning, Human Resources, Scientific Work, Security Specialist, Solicitor, Planner, Production Administrator.
21	D-S-C	Director	Seorang yang obyektif dan analitis.  Ia ingin terlibat dalam situasi, dan ia juga ingin memberikan bantuan dan dukungan kepada orang yang ia hormati.  Secara internal termotivasi oleh target pribadi, ia berorientasi terhadap pekerjaannya tapi juga menyukai hubungan dengan sesama.  Karena determinasinya yang kuat, ia sering berhasil dalam berbagai hal; karakternya yang tenang, stabil dan daya tahannya yang tinggi memiliki kontribusi dalam keberhasilannya.  Ulet dalam memulai pekerjaan. Ia akan berusaha keras untuk mencapai sasarannya.  Seorang yang mandiri dan cermat serta memiliki tindak lanjut yang baik.	2026-09-04 08:15:20	2026-09-04 08:15:20	Engineering and Production (Directing, Managing, Supervising), Sales, Sales Management, Service Manager, Distribution, Public Relations, Creative Designer, Office Management, Chief Engineer, Business Consultant, Chief Financial Officer, Customer Service, National Accounts Manager, Chief Accountant, Lecturer, Projects Manager, Research Planning, Human Resources, Scientific Work, Security Specialist, Solicitor, Planner, Production Administrator.
22	D-C-I	CHALLENGER	Seorang yang sensitif terhadap permasalahan, dan memiliki kreativitas yang baik dalam memecahkan masalah. Ia dapat menyelesaikan tugas-tugas penting dalam waktu singkat karena mempunyai keputusan yang kuat. Seorang yang tekun dan memiliki reaksi yang cepat.  Ia akan meneliti dan mengejar semua kemungkinan yang ada dalam mencari solusi permasalahan.  Ia banyak memberikan ide-ide dengan berfokus pada pekerjaan. Usaha yang keras pada ketepatan akan mengimbangi keinginannya pada hasil yang terukur.  Ia cenderung perfeksionis dan dapat juga memperlambat pengambilan keputusan karena keinginannya untuk menentukan pilihan yang terbaik.	2026-09-04 08:15:20	2026-09-04 08:15:20	Technical/Scientific (Directing, Management, Supervision), Engineering, Finance, Production Planning, Personnel Disciplines, Self-Employment, Credit Manager, Planner, Lecturing, Work Study, Transport, Consultancy, Industrial Relations and Computers (Selling, Software, Systems Analyst) and General Manager.
23	D-C-S	CHALLENGER	Seorang yang sensitif terhadap permasalahan, dan memiliki kreativitas yang baik dalam memecahkan masalah. Ia dapat menyelesaikan tugas-tugas penting dalam waktu singkat karena mempunyai keputusan yang kuat. Seorang yang tekun dan memiliki reaksi yang cepat.  Ia akan meneliti dan mengejar semua kemungkinan yang ada dalam mencari solusi permasalahan.  Ia banyak memberikan ide-ide dengan berfokus pada pekerjaan. Usaha yang keras pada ketepatan akan mengimbangi keinginannya pada hasil yang terukur.  Ia cenderung perfeksionis dan dapat juga memperlambat pengambilan keputusan karena keinginannya untuk menentukan pilihan yang terbaik.	2026-09-04 08:15:20	2026-09-04 08:15:20	Engineering, Production and Finance (Directing, Administrating, Managing and Managing Specialist Work), Scientific, Research Planning, Personnel, Trouble Shooting, Credit Control, Chief Accountant, Accountant, Chief Engineer, Work Study, Consultancy, Designer, Draughtsman, Project Work, Security Specialist, Doctor, Attorney.
24	I	COMMUNICATOR	Merupakan seorang yang antusias dan optimistik, ia lebih suka mencapai sasarannya melalui orang lain. Ia suka berhubungan dengan sesamanya - ia bahkan suka mengadakan “pesta” atau kegiatan untuk berkumpul, dan ini menunjukkan kepribadiannya yang ramah. Ia tidak suka bekerja sendirian dan cenderung bersama dengan orang lain dalam menyelesaikan proyek.  Perhatian dan fokusnya tidak sebaik apa yang dia inginkan -  maka ia membutuhkan energi yang besar untuk mampu bergerak cepat dari satu hal ke hal berikutnya tanpa penundaan.  Ia sangat menonjol dalam keterampilan berkomunikasi, dan ini merupakan salah satu kekuatan yang paling sering digunakan.  Ia memiliki kemampuan untuk memotivasi dan memberi semangat dengan kata-katanya, dan ia dikenal sebagai individu yang inspirasional. Ketika ia harus memusatkan perhatiannya pada tugas, Ia akan menjadi tidak akurat dan bahkan tidak terorganisir.  Tetapi ia akan memusatkan perhatian kepada yang harus ia senangkan, karena ia enggan sekali untuk menolak.  Ia menginginkan pengakuan sosial dan takut akan penolakan.  Ia mudah menemukan teman dan berusaha menciptakan suasana yang menyenangkan.  Ia membutuhkan seorang manajer atau supervisor untuk menentukan batas waktu yang jelas dalam pekerjaannya, ia lebih suka menggunakan gaya manajemen partisipatif yang dibangun berdasarkan hubungan yang kuat.	2026-09-04 08:15:20	2026-09-04 08:15:20	Promoting, Demonstrating, Canvassing, Marketing Services, Public Relations, Lecturing, Advertising, Publican, Publishing, Hospitality, Retail-General, Human Resources, Journalist, Singers, Technical Writing, Tour Guide, Promotional Work, Hotelier, Dancers, Host, Actors, Travel Agent, Politician, and very soft selling.
25	I-S	ADVISOR	Seorang yang mengesankan orang akan kehangatan, simpati dan pengertiannya.  Ia memiliki ketenangan dalam sebagian besar situasi sosial dan jarang tidak menyenangkan orang lain.  Faktanya, banyak orang datang padanya karena ia kelihatan sebagai pendengar yang baik.  Ia cenderung sangat demonstratif dan emosinya biasanya tampak jelas bagi orang di sekitarnya.  Ia tidak akan memaksakan idenya pada orang lain; ia tidak tegas dalam mengekspresikan atau memberi perintah.  Jika ia sangat kuat merasakan sesuatu, Ia akan bicara secara terbuka dan terus terang tentang pendiriannya.  Ia cenderung menerima kritik atas pekerjaannya sebagai serangan pribadi.  Ia dapat menjadi sangat toleran dan sabar kepada mereka yang tidak produktif di pekerjaan.  Ia merupakan \\"penjaga damai\\" dan akan bekerja untuk menjaga kedamaian dalam setiap keadaan.	2026-09-04 08:15:20	2026-09-04 08:15:20	Personnel, Welfare, Training, Hotelier, Promoting, Travel Agent, Lecturing, Upmarket/Speciality Sales, Soft/Service Selling, Beauty Therapist, Psychologist, Nursing, Human Resources, Retail-Specialist, Veterinarian, Social Work, Personal Assistant, Personnel-HR, Coach, Mentor.
26	I-C	ASSESSOR	Merupakan seorang yang ramah dan suka berteman; ia merasa nyaman walaupun dengan orang asing. Ia dapat mengembangkan hubungan baru dengan mudah, dan pada umumnya dapat mengendalikan diri sampai pada tingkat dimana ia jarang menimbulkan rasa benci pada orang lain dengan sengaja. Ia seorang yang sangat sosial, menunjukkan kepedulian dan persahabatan ketika sedang melakukan tugas-tugas di tangannya. Ia cenderung perfeksionis secara alamiah, dan akan mengisolasi dirinya jika diperlukan untuk melaksanakan pekerjaan.  Ia berkeinginan mempromosikan tugas-tugas orang lain, juga kepunyaannya.  Kadang-kadang ia salah menilai kemampuan orang lain dikarenakan pandangan-pandangannya yang optimis.	2026-09-04 08:15:20	2026-09-04 08:15:20	Teaching, Training, Inventing, Specialist Selling (Engineering, Finance or any area involving capital equipment), Project Engineer, Finance, Service Engineer or Supervising within a Technical/Specialist Area, Public Relations, Environmentalist, Marketing, Conference Organiser, Estate Agent.
27	I-C-D	ASSESSOR	Merupakan seseorang yang analitis, berwatak hati-hati dan ramah pada saat merasa nyaman. Ia sangat biasa dengan orang asing, karena ia dapat menilai dan menyesuaikan diri dalam hubungan mereka. Ia dapat mengembangkan hubungan baru dengan mudah ketika ia ingin melakukannya, dan pada umumnya dapat mengendalikan diri sampai pada tingkat di mana ia jarang menimbulkan rasa benci pada orang lain dengan sengaja. Ia menampilkan sikap peduli dan ramah, namun mampu memusatkan perhatian pada penyelesaian tugas yang ada. Ia cenderung perfeksionis secara alami, dan akan mengisolasi dirinya jika diperlukan untuk melaksanakan pekerjaan. Ia suka berada pada situasi yang dapat diramalkan dan tidak ada kejutan. Ia sangat berorientasi pada kualitas dan akan bekerja dengan keras untuk menyelesaikan pekerjakan dengan benar. Ia ingin orang-orang berkenan akan pekerjaan yang sudah ia selesaikan dengan baik.	2026-09-04 08:15:20	2026-09-04 08:15:20	Specialist/Technical Selling (Computer, Finance, Engineer and others, Technical/Capital Equipment Selling), Financial (Manager, Specialist), Engineering (Manager, Designer, Buyer, Draughtsman), Project Engineer, Sales Engineer, Consultant, Trainer, Lecturer, Hotelier, Travel Agent, Personnel and Marketing Services.
28	I-C-S	RESPONSIVE & THOUGHTFUL	Merupakan individu yang berorientasi pada orang dan lancar berkomunikasi serta loyal.  Ia cenderung sensitif dan mempunyai standard yang tinggi.  Keputusannya dibuat berdasarkan fakta dan data pendukung.  Ia sepertinya tidak bisa diam.  Ia perlu untuk lebih terus terang dan jangan terlalu subyektif.  Ia butuh pengakuan sosial dan perhatian pribadi; ia dapat cepat akrab dengan orang lain.  Ia bersahabat, antusias, informal, banyak bicara dan terlalu khawatir terhadap apa yang dipikirkan orang.  Ia menguasai banyak hal.  Ia ingin diterima sebagai anggota kelompok dan ingin mengetahui secara pasti apa yang diharapkan darinya sebelum ia memulai proyek baru.	2026-09-04 08:15:20	2026-09-04 08:15:20	Personnel, Welfare, Training, Attorney, Teaching, Accounting, Technical Instructor, Customer Services, Public Relations, Artist, Hotelier, Demonstrator, Engineering (Sales, Service, Project, Draughtsman, Designer), Specialist (Soft/Services), Selling, Purchasing, Supervising (Engineering, Production, Accounts), Administration Work, Secretarial, Industrial Relations Specialist.
29	S-D	SELF-MOTIVATED	Merupakan seorang yang obyektif dan analitis.  Ia ingin terlibat dalam situasi, dan juga ingin memberikan bantuan dan dukungan.  Secara internal termotivasi oleh target pribadi, Ia menyukai orang-orang, tetapi juga mempunyai kemampuan untuk berorientasi pada pekerjaannya pada saat dibutuhkan.  Karena determinasinya yang kuat, ia sering berhasil dalam berbagai hal; karakternya yang tenang, stabil dan daya tahannya memiliki kontribusi akan keberhasilannya.  Keuletannya setelah memulai pekerjaan, ia akan berusaha keras untuk mendapatkan sasarannya.  Seorang yang bebas, ia orang yang cermat dan memiliki tindak lanjut yang baik.  Ia bisa menjadi tidak ramah walaupun ia pada dasarnya ia yang berorientasi pada orang; dan pada situasi yang tidak membuatnya nyaman, ia lebih suka mendukung pemimpinnya dari pada keterlibatannya dengan situasi.	2026-09-04 08:15:20	2026-09-04 08:15:20	Investigator, Researcher, Accountant, Engineering, Production/Engineering Supervisor, Computer Specialist, Architect, Transport/Warehouse Supervisor, Credit Controller, DP Supervisor, Computer Specialist, Research and Development, Private Investigator, Quality Controller, Engineering (Designer, Draughtsman, Project Engineer), Sales and Service Engineer, Property Manager, Attorney, Administration Manager
30	S-I	ADVISOR	Seorang yang mengesankan orang akan kehangatan, simpati dan pengertiannya.  Ia memiliki ketenangan dalam sebagian besar situasi sosial dan jarang tidak menyenangkan orang lain.  Faktanya, banyak orang datang padanya karena ia kelihatan sebagai pendengar yang baik.  Ia cenderung sangat demonstratif dan emosinya biasanya tampak jelas bagi orang di sekitarnya.  Ia tidak akan memaksakan idenya pada orang lain; ia tidak tegas dalam mengekspresikan atau memberi perintah.  Jika ia sangat kuat merasakan sesuatu, Ia akan bicara secara terbuka dan terus terang tentang pendiriannya.  Ia cenderung menerima kritik atas pekerjaannya sebagai serangan pribadi.  Ia dapat menjadi sangat toleran dan sabar kepada mereka yang tidak produktif di pekerjaan.  Ia merupakan \\"penjaga damai\\" yang sebenarnya dan akan bekerja untuk menjaga kedamaian dalam setiap keadaan.	2026-09-04 08:15:20	2026-09-04 08:15:20	Personnel Welfare, Training, Hotelier, Promoting, Travel Agent, Lecturing, Child Care, Charitable Organizations, Soft or Service Selling, Psychologist, Therapist, Nurse, Personal Assistant, Hospitality Manager, Social Work, Student Services, Upmarket/Speciality Sales.
31	S-D-I	DIRECTOR	Seorang yang obyektif dan analitis.  Ia ingin terlibat dalam situasi, dan ia juga ingin memberikan bantuan dan dukungan kepada orang yang ia hormati.  Secara internal termotivasi oleh target pribadi, ia berorientasi terhadap pekerjaannya tapi juga menyukai hubungan dengan sesama.  Karena determinasinya yang kuat, ia sering berhasil dalam berbagai hal; karakternya yang tenang, stabil dan daya tahannya yang tinggi memiliki kontribusi dalam keberhasilannya.  Ulet dalam memulai pekerjaan. Ia akan berusaha keras untuk mencapai sasarannya.  Seorang yang mandiri dan cermat serta memiliki tindak lanjut yang baik.	2026-09-04 08:15:20	2026-09-04 08:15:20	Engineering and Production (Supervision), Service Selling, Distribution and Warehouse Supervision/Manager, Office Management, Customer Service, System Analyst, Radio Announcer, Technical Writing, Telemarketing, TV Presenter, Project Engineer, Film Producer, Programmer, Sales/Service Engineer, Accounting, Draughtsman, Project Engineer.
32	S-I-D	ADVISOR	Seorang yang mengesankan orang akan kehangatan, simpati dan pengertiannya.  Ia memiliki ketenangan dalam sebagian besar situasi sosial dan jarang tidak menyenangkan orang lain.  Faktanya, banyak orang datang padanya karena ia kelihatan sebagai pendengar yang baik.  Ia cenderung sangat demonstratif dan emosinya biasanya tampak jelas bagi orang di sekitarnya.  Ia tidak akan memaksakan idenya pada orang lain; ia tidak tegas dalam mengekspresikan atau memberi perintah.  Jika ia sangat kuat merasakan sesuatu, Ia akan bicara secara terbuka dan terus terang tentang pendiriannya.  Ia cenderung menerima kritik atas pekerjaannya sebagai serangan pribadi.  Ia dapat menjadi sangat toleran dan sabar kepada mereka yang tidak produktif di pekerjaan.  Ia merupakan \\"penjaga damai\\" yang sebenarnya dan akan bekerja untuk menjaga kedamaian dalam setiap keadaan.	2026-09-04 08:15:20	2026-09-04 08:15:20	Engineering and Production (Supervision), Service Selling, Distribution and Warehouse Supervision, Office Management, Customer Service, System Analyst, Programmer, Sales/Service Engineer, Accounting, Draughtsman, Project Engineer.
33	S-I-C	ADVOCATE	Merupakan orang yang stabil, individu yang ramah yang berusaha keras membangun hubungan yang positif di tempat kerja dan di rumah.  Ia dapat menjadi sangat berorientasi detil ketika situasi membutuhkan; tetapi secara keseluruhan ia cenderung individualis, independen dan sedikit perhatian terhadap detil.  Sekali dia membuat keputusan, sangat sulit mengubah pendiriannya.  Ia menyukai hubungan dengan orang dan cenderung mendukung pihak yang lemah.  Ia akan mengambil posisi berlawanan dengan ketidaksepakatan dan merasa frustrasi jika sesuatu tidak sejalan dengannya.  Ia ingin diterima sebagai anggota tim, dan ia menginginkan orang lain menyukainya.  Ia cukup sulit membuat keputusan sampai parameter wewenang secara jelas ditentukan, dan ia mungkin cenderung tidak sungguh-sungguh jika dipaksa membuat keputusan ketika ia tidak ingin melakukannya.  Ia menginginkan orang lain yang membuat keputusan, khususnya jika ada orang yang sangat ia hargai dan hormati.  Ia cenderung moderat, cermat dan dapat diandalkan.	2026-09-04 08:15:20	2026-09-04 08:15:20	Personnel Welfare, Training, Teaching, Attorney, Accounting, Technical Instructor, Customer Service, Public Relations, Artist, Hotelier, Demonstrator, Engineer (Sales, Service, Project, Draughtsman, Designer), Specialist (Soft/Service), Selling, Purchasing, Supervising (Engineering, Production, Accounts) Administrative Work, Secretarial.
34	S-C-D	INQUIRER	Seorang yang baik secara alamiah dan sangat berorientasi detil.  Ia peduli dengan orang-orang di sekitarnya dan mempunyai kualitas yang membuatnya sangat teliti dalam penyelesaian tugas.  Ia mempertimbangkan sekelilingnya dengan hati-hati sebelum membuat keputusan untuk melihat pengaruhnya pada mereka; saat tertentu ia terlalu hati-hati.  Jika ia merasa seseorang memanfaatkan situasi, ia akan memperlambat kerjanya sehingga dapat mengamati apa yang sedang berlangsung di sekitarnya.	2026-09-04 08:15:20	2026-09-04 08:15:20	Directing, Managing or Supervising (in Engineering, Accountancy, Research and Development and Computing disciplines), Accountant, Project Engineer, Draughtsman, Designer, Analyst, Chemist, Technician, Service Engineer, Manager, Security Specialist.
35	S-C-I	ADVOCATE	Merupakan orang yang stabil, individu yang ramah yang berusaha keras membangun hubungan yang positif di tempat kerja dan di rumah.  Ia dapat menjadi sangat berorientasi detil ketika situasi membutuhkan; tetapi secara keseluruhan ia cenderung individualis, independen dan sedikit perhatian terhadap detil.  Sekali dia membuat keputusan, sangat sulit mengubah pendiriannya.  Ia menyukai hubungan dengan orang dan cenderung mendukung pihak yang lemah.  Ia akan mengambil posisi berlawanan dengan ketidaksepakatan dan merasa frustrasi jika sesuatu tidak sejalan dengannya.  Ia ingin diterima sebagai anggota tim, dan ia menginginkan orang lain menyukainya.  Ia cukup sulit membuat keputusan sampai parameter wewenang secara jelas ditentukan, dan ia mungkin cenderung tidak sungguh-sungguh jika dipaksa membuat keputusan ketika ia tidak ingin melakukannya.  Ia menginginkan orang lain yang membuat keputusan, khususnya jika ada orang yang sangat ia hargai dan hormati.  Ia cenderung moderat, cermat dan dapat diandalkan.	2026-09-04 08:15:20	2026-09-04 08:15:20	Personnel Welfare, Administrator, Advisers, Training, Teaching, Attorney, Accounting, Counseling, Technical Instructor, Customer Service, Accounting-General, Public Relations, Accounts Supervisor, Artist, Hotelier, Demonstrator, Engineer (Sales, Service, Project, Draughtsman, Designer), Specialist (Soft/Service), Selling, Purchasing, Sales Engineer, Legal, Negotiator, Student Service, Photographer, Physiotherapist, Project Engineer, Vocational Education, Supervising (Engineering, Production, Accounts) Administrative Work, Demonstrator, Secretarial, Hospitality Manager.
36	C-I	ASSESSOR	Merupakan seseorang yang analitis, berwatak hati-hati dan ramah pada saat merasa nyaman. Ia sangat biasa dengan orang asing, karena ia dapat menilai dan menyesuaikan diri dalam hubungan mereka. Ia dapat mengembangkan hubungan baru dengan mudah ketika ia ingin melakukannya, dan pada umumnya dapat mengendalikan diri sampai pada tingkat di mana ia jarang menimbulkan rasa benci pada orang lain dengan sengaja. Ia menampilkan sikap peduli dan ramah, namun mampu memusatkan perhatian pada penyelesaian tugas yang ada. Ia cenderung perfeksionis secara alami, dan akan mengisolasi dirinya jika diperlukan untuk melaksanakan pekerjaan. Ia suka berada pada situasi yang dapat diramalkan dan tidak ada kejutan. Ia sangat berorientasi pada kualitas dan akan bekerja dengan keras untuk menyelesaikan pekerjakan dengan benar. Ia ingin orang-orang berkenan akan pekerjaan yang sudah ia selesaikan dengan baik.	2026-09-04 08:15:20	2026-09-04 08:15:20	Sales (Technical/Specialist), Public Relations, Lecturer, Academic, Personnel Administration, Purchasing, Travel Agent, Training, Teaching, Real Estate Agent, Hospitality Administration, Sales-Technical, Hotelier, Project Engineer, Service Engineer.
37	C-D-I	CHALLENGER	Seorang yang sangat berorientasi pada tugas dan sensitif pada permasalahan. Ia lebih mempedulikan tugas yang ada dibanding orang-orang di sekitarnya, termasuk perasaan mereka. Ia sangat kukuh/keras dan mempunyai pendekatan yang efektif dalam pemecahan masalah. Oleh karena sifat alamiah dan keinginannya akan hasil yang terukur, ia akan tampak dingin, tidak berperasaan dan menjaga jarak. Ia membuat keputusan berdasar pada fakta, bukan emosi. ia cenderung pendiam dan tidak mudah percaya.	2026-09-04 08:15:20	2026-09-04 08:15:20	Directing, Managing or Supervising (Engineering, Research, Finance, Planning), Designer, Work Study, Sales (Technical/ Specialist), Logistic Support, Systems Analyst, Lecturer, Company Secretary, Negotiator and Purchasing.
38	C-D-S	CONTEMPLATOR	Berorientasi pada hal detil dan mempunyai standard tinggi untuk dirinya. Ia logis dan analitis. Ia ingin berbuat yang terbaik, dan ia selalu berpikir ada ruang untuk peningkatan/kemajuan. Ia cenderung kompetitif dan ingin menghasilkan pekerjaan dengan mutu yang terbaik. Ia sebenarnya sensitif terhadap orang-orang, tetapi karena sifat logisnya, orientasinya terhadap tugas dapat menutupinya dengan mudah. Ia suka dihargai untuk pekerjaannya yang berkualitas. Ia mampu mengerjakan tugas-tugas; dan mencapai sasarannya. Ia sangat memusatkan perhatian pada tugas yang ada, mantap dan dapat diandalkan.	2026-09-04 08:15:20	2026-09-04 08:15:20	Engineering, Research, Production and Finance (Director, Manager atau Supervisor), Work Study, Accountant, Administrator, Quality Controller, Safety Officer, Market Analyst, Planner and Personnel (Director, Manager, Administrator), MIS Manager, Security Manager, Loss Control.
39	C-I-D	ASSESSOR	Merupakan seseorang yang analitis, berwatak hati-hati dan ramah pada saat merasa nyaman. Ia sangat biasa dengan orang asing, karena ia dapat menilai dan menyesuaikan diri dalam hubungan mereka. Ia dapat mengembangkan hubungan baru dengan mudah ketika ia ingin melakukannya, dan pada umumnya dapat mengendalikan diri sampai pada tingkat di mana ia jarang menimbulkan rasa benci pada orang lain dengan sengaja. Ia menampilkan sikap peduli dan ramah, namun mampu memusatkan perhatian pada penyelesaian tugas yang ada. Ia cenderung perfeksionis secara alami, dan akan mengisolasi dirinya jika diperlukan untuk melaksanakan pekerjaan. Ia suka berada pada situasi yang dapat diramalkan dan tidak ada kejutan. Ia sangat berorientasi pada kualitas dan akan bekerja dengan keras untuk menyelesaikan pekerjakan dengan benar. Ia ingin orang-orang berkenan akan pekerjaan yang sudah ia selesaikan dengan baik.	2026-09-04 08:15:20	2026-09-04 08:15:20	Directing, Managing or Supervising (Engineering, Research, Finance, Planning), Designer, Work Study, Sales (Technical/Specialist), Lecturer, Company Secretary, Negotiator and Purchasing.
40	C-S-D	PRECISIONIST	Berpikir sistematis dan cenderung mengikuti prosedur dalam kehidupan pribadi dan pekerjaannya.  Teratur dan memiliki perencanaan yang baik, ia teliti dan fokus pada detil.  Ia bertindak dengan penuh kebijaksanaan, diplomatis dan jarang menentang rekan kerjanya dengan sengaja.  Ia sangat berhati-hati, ia sungguh-sungguh mengharapkan akurasi dan standard tinggi dalam pekerjaannya.  Ia cenderung terjebak dalam hal detil, khususnya jika harus memutuskan.  ia menginginkan adanya petunjuk standard pelaksanaan kerja dan tanpa perubahan mendadak.	2026-09-04 08:15:20	2026-09-04 08:15:20	Engineering, Research Director, Production and Finance (Director, Manager, Supervisor), Work Study, Accountant, Administrator, Quality Controller, Financial Services Manager, Safety Officer, Market Analyst, Planner and Personnel (Director, Manager, Administrator), MIS Manager, Electrician, Security Manager, Financial Researcher, Planner, Printer, Production Controller, Production Manager, Personnel Management, Loss Control.
\.


--
-- Data for Name: disc_test_results; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.disc_test_results (id, test_attempt_id, disc_profiles_id, line_1_scores, line_2_scores, line_3_scores, created_at, updated_at) FROM stdin;
1	9	3	{"raw":{"D":7,"I":4,"S":4,"C":4,"*":5},"converted":{"D":0.5,"I":1,"S":-0.7,"C":0.5}}	{"raw":{"D":5,"I":5,"S":6,"C":4,"*":4},"converted":{"D":0.5,"I":0,"S":0.5,"C":2.5}}	{"raw":{"D":2,"I":-1,"S":-2,"C":0},"converted":{"D":0.7,"I":0,"S":-0.5,"C":1.5}}	2026-09-04 10:23:26	2026-09-04 10:23:26
2	11	3	{"raw":{"D":4,"I":3,"S":3,"C":6,"*":8},"converted":{"D":-1.7,"I":-1.3,"S":-1.5,"C":3}}	{"raw":{"D":3,"I":7,"S":4,"C":5,"*":5},"converted":{"D":2.5,"I":-3.5,"S":2.5,"C":1.5}}	{"raw":{"D":1,"I":-4,"S":-1,"C":1},"converted":{"D":0.5,"I":-3,"S":0,"C":3}}	2026-09-04 10:24:39	2026-09-04 10:24:39
3	10	16	{"raw":{"D":3,"I":2,"S":5,"C":6,"*":8},"converted":{"D":-2.5,"I":-2.5,"S":0.5,"C":3}}	{"raw":{"D":10,"I":5,"S":4,"C":1,"*":4},"converted":{"D":-3,"I":0,"S":2.5,"C":7}}	{"raw":{"D":-7,"I":-3,"S":1,"C":5},"converted":{"D":-3,"I":-2,"S":1.5,"C":5.7}}	2026-09-04 10:24:39	2026-09-04 10:24:39
4	15	3	{"raw":{"D":9,"I":2,"S":0,"C":8,"*":5},"converted":{"D":2,"I":-2.5,"S":-5.7,"C":5.7}}	{"raw":{"D":2,"I":9,"S":6,"C":3,"*":4},"converted":{"D":4.3,"I":-5.3,"S":0.5,"C":4}}	{"raw":{"D":7,"I":-7,"S":-6,"C":5},"converted":{"D":2.5,"I":-4.7,"S":-3,"C":5.7}}	2026-09-04 10:24:40	2026-09-04 10:24:40
5	13	26	{"raw":{"D":1,"I":8,"S":2,"C":4,"*":9},"converted":{"D":-5.3,"I":5.7,"S":-3.5,"C":0.5}}	{"raw":{"D":8,"I":3,"S":3,"C":5,"*":5},"converted":{"D":-1.5,"I":2.5,"S":4,"C":1.5}}	{"raw":{"D":-7,"I":5,"S":-1,"C":-1},"converted":{"D":-3,"I":4.3,"S":0,"C":0.5}}	2026-09-04 10:24:40	2026-09-04 10:24:40
6	5	5	{"raw":{"D":8,"I":5,"S":4,"C":5,"*":2},"converted":{"D":1,"I":3,"S":-0.7,"C":2}}	{"raw":{"D":7,"I":5,"S":8,"C":3,"*":1},"converted":{"D":-1.3,"I":0,"S":-2,"C":4}}	{"raw":{"D":1,"I":0,"S":-4,"C":2},"converted":{"D":0.5,"I":0.5,"S":-1.5,"C":4}}	2026-09-04 10:24:40	2026-09-04 10:24:40
7	6	4	{"raw":{"D":4,"I":8,"S":3,"C":4,"*":5},"converted":{"D":-1.7,"I":5.7,"S":-1.5,"C":0.5}}	{"raw":{"D":3,"I":1,"S":7,"C":7,"*":6},"converted":{"D":2.5,"I":6,"S":-1.3,"C":0}}	{"raw":{"D":1,"I":7,"S":-4,"C":-3},"converted":{"D":0.5,"I":5.5,"S":-1.5,"C":0}}	2026-09-04 10:24:40	2026-09-04 10:24:40
8	16	16	{"raw":{"D":2,"I":0,"S":7,"C":10,"*":5},"converted":{"D":-4,"I":-7,"S":2.5,"C":6.3}}	{"raw":{"D":5,"I":8,"S":5,"C":2,"*":4},"converted":{"D":0.5,"I":-4.3,"S":1.5,"C":5.6}}	{"raw":{"D":-3,"I":-8,"S":2,"C":8},"converted":{"D":-1,"I":-5.7,"S":2,"C":6.5}}	2026-09-04 10:24:40	2026-09-04 10:24:40
9	14	5	{"raw":{"D":10,"I":5,"S":2,"C":4,"*":3},"converted":{"D":3,"I":3,"S":-3.5,"C":0.5}}	{"raw":{"D":4,"I":5,"S":8,"C":2,"*":5},"converted":{"D":1.5,"I":0,"S":-2,"C":5.6}}	{"raw":{"D":6,"I":0,"S":-6,"C":2},"converted":{"D":2,"I":0.5,"S":-3,"C":4}}	2026-09-04 10:24:40	2026-09-04 10:24:40
10	17	16	{"raw":{"D":6,"I":3,"S":2,"C":8,"*":5},"converted":{"D":0,"I":-1.3,"S":-3.5,"C":5.7}}	{"raw":{"D":6,"I":9,"S":2,"C":4,"*":3},"converted":{"D":0,"I":-5.3,"S":6,"C":2.5}}	{"raw":{"D":0,"I":-6,"S":0,"C":4},"converted":{"D":0,"I":-4.3,"S":1,"C":5.5}}	2026-09-04 10:59:24	2026-09-04 10:59:24
\.


--
-- Data for Name: disc_traits; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.disc_traits (id, dimension_code, potret_diri, kelebihan, kekurangan, deskripsi_tipe, kecenderungan, lingkungan_cocok, created_at, updated_at) FROM stdin;
1	D         	["Independen dan percaya diri","Berorientasi pada target dan hasil","Suka tantangan dan wewenang","Cepat dalam mengambil keputusan"]	["Kepemimpinan yang kuat dalam situasi krisis","Berani mengambil risiko","Fokus pada pencapaian hasil","Mengatasi hambatan dengan cepat"]	["Terkadang kurang sabar dengan orang lain","Tampak terlalu mendominasi atau keras","Kurang memperhatikan detail administratif","Cenderung abaikan perasaan orang lain"]	Dimensi Dominance menekankan pada membentuk lingkungan dengan mengatasi hambatan untuk mencapai hasil.	["Mendapatkan hasil secepatnya","Menantang status quo","Mengambil alih kendali"]	["Lingkungan yang dinamis dan kompetitif","Memiliki otonomi dan kebebasan bertindak","Peluang untuk kemajuan karir yang cepat"]	2026-08-26 13:52:42	2026-08-26 13:52:42
2	I         	["Antusias, ramah, dan optimis","Pandai membangun komunikasi","Menyukai popularitas dan interaksi","Kreatif dan penuh energi"]	["Memotivasi dan menginspirasi orang lain","Kreatif dalam mencari ide baru","Membangun jejaring sosial yang luas","Memiliki rasa humor yang positif"]	["Cenderung terburu-buru dan kurang detail","Terlalu banyak berjanji namun kurang eksekusi","Bosan dengan rutinitas administratif","Sangat tergantung pada pengakuan sosial"]	Dimensi Influence menekankan pada membentuk lingkungan dengan mempengaruhi atau membujuk orang lain.	["Menghubungi orang secara spontan","Membuat kesan menguntungkan","Gaya bicara yang persuasif"]	["Lingkungan kerja sosial dan kolaboratif","Bebas dari kontrol ketat atau rincian rutin","Pengakuan dan penghargaan publik"]	2026-08-26 13:52:42	2026-08-26 13:52:42
3	S         	["Tenang, sabar, dan setia","Pendengar yang sangat baik","Pekerja keras yang konsisten","Menyukai iklim kerja harmonis"]	["Dapat diandalkan dan konsisten","Sabar dan mampu mendamaikan konflik","Membangun hubungan jangka panjang","Anggota tim yang kooperatif"]	["Resisten terhadap perubahan mendadak","Lambat dalam mengambil keputusan","Terkadang menahan ketidakpuasan","Meninggalkan inisiatif pribadi"]	Dimensi Steadiness menekankan pada bekerja sama dengan orang lain dalam kondisi yang ada untuk melaksanakan tugas.	["Menjaga konsistensi dan stabilitas","Mengembangkan kebiasaan kerja yang baik","Menunjukkan rasa empati"]	["Lingkungan kerja yang stabil dan dapat diprediksi","Suasana kerja yang harmonis tanpa konflik tajam","Prosedur kerja yang jelas"]	2026-08-26 13:52:42	2026-08-26 13:52:42
4	C         	["Analitis, teliti, dan sistematis","Menjaga standar kualitas tinggi","Berhati-hati dalam mengambil keputusan","Berorientasi pada data dan fakta"]	["Akurasi dan ketelitian tinggi","Pemikiran logis dan objektif","Menjaga standar dan ketaatan prosedur","Perencanaan terstruktur"]	["Terlalu perfeksionis dan kritis","Cenderung lambat karena perbanyak analisa","Kaku terhadap aturan","Tertutup secara emosional"]	Dimensi Compliance menekankan pada bekerja secara tekun dalam kondisi yang ada untuk memastikan kualitas dan ketelitian.	["Menganalisis data secara mendalam","Menjaga standar akurasi yang tinggi","Mengikuti instruksi dan prosedur"]	["Lingkungan yang memerlukan ketelitian dan analisis","Standard Operational Procedure (SOP) yang jelas","Fokus pada kualitas produk\\/layanan"]	2026-08-26 13:52:42	2026-08-26 13:52:42
\.


--
-- Data for Name: educations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.educations (id, profile_id, school_name, degree, major, study_program, start_year, end_year, gpa, description, degree_id, major_id) FROM stdin;
1	1	Politeknik Negeri Semarang	\N	\N	Teknologi Rekayasa Komputer	2023	\N	3.80	\N	3	1
2	1	SMA 3 Pemalang	\N	\N	IPA	2019	2023	90.00	\N	1	\N
3	2	SMA 2 UNGARAN	\N	\N	IPA	2022	2026	96.00	\N	1	\N
4	3	Polines	\N	\N	TRK	2023	\N	3.80	\N	3	4
\.


--
-- Data for Name: employee_profiles; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.employee_profiles (id, user_id, nik, full_name, company_id, department_id, position_title, phone_number, gender, birth_date, created_at, updated_at, photo, employee_type, position_id) FROM stdin;
1	5	3322192965320002	Syauqi Maul	1	2	Sales	\N	\N	\N	2026-09-01 10:43:10	2026-09-01 10:43:10	\N	permanent	\N
2	6	3374124506980002	DWI LESTARI INDAH SARI	1	2	Magang	\N	\N	\N	2026-09-01 11:04:05	2026-09-01 11:04:05	\N	permanent	\N
5	2	3374000011112222	Ilham Taruprasetyo	1	2	Sales	\N	\N	\N	2026-09-01 11:38:08	2026-09-01 11:38:32	\N	permanent	\N
6	9	3306084412040001	Dita Tri Handayani	1	2	Magang	\N	\N	\N	2026-09-01 11:43:00	2026-09-03 08:35:39	\N	internship	\N
7	10	3175065210040016	Hasna Nur Aisyah Makarim	1	2	Magang	\N	\N	\N	2026-09-01 11:44:10	2026-09-03 08:35:49	\N	internship	\N
8	11	3309067105980001	Galuh Prasetya Ningrum	1	2	Magang	\N	\N	\N	2026-09-02 10:31:43	2026-09-03 08:35:58	\N	internship	\N
9	12	3374066207020002	Anggun Widiasari	1	2	Magang	\N	\N	\N	2026-09-02 10:31:59	2026-09-03 08:36:09	\N	internship	\N
12	15	3327001234567890	Faishal Sitohang	1	2	CEO	\N	\N	\N	2026-09-02 15:11:04	2026-09-03 08:39:44	\N	internship	\N
11	14	3323071308980001	Samsul Hidayat	1	2	Magang	\N	\N	\N	2026-09-02 12:29:57	2026-09-04 10:41:27	\N	internship	\N
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: interview_schedule; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.interview_schedule (id, job_applications_id, users_id, interview_date, location, meeting_link, status) FROM stdin;
\.


--
-- Data for Name: job_applications; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.job_applications (id, job_id, profile_id, status, applied_at, notes, recruiter_approval, recruiter_notes, recruiter_approved_at, recruiter_id, admin_approval, admin_notes, admin_approved_at, admin_id) FROM stdin;
3	2	1	Shortlisted	2026-08-26 21:34:28	Selamat! Anda lolos seleksi berkas administrasi. Silakan lanjut kerjakan ujian online yang tersedia pada menu Riwayat Lamaran.	pending	\N	\N	\N	pending	\N	\N	\N
2	1	1	Shortlisted	2026-08-26 20:59:00	Selamat! Anda lolos seleksi berkas administrasi. Silakan lanjut kerjakan ujian online yang tersedia pada menu Riwayat Lamaran.	pending	\N	\N	\N	pending	\N	\N	\N
4	4	1	Reviewed	2026-08-27 10:06:45	Riza Putri Puspita Dewi	pending	\N	\N	\N	pending	\N	\N	\N
1	3	1	Reviewed	2026-08-26 14:13:09	Ma'ruf Cristi Aji	pending	\N	\N	\N	pending	\N	\N	\N
5	1	2	Reviewed	2026-09-01 15:11:35	Selamat! Anda lolos seleksi berkas administrasi. Silakan lanjut kerjakan ujian online yang tersedia pada menu Riwayat Lamaran.	pending	\N	\N	\N	pending	\N	\N	\N
6	3	2	Reviewed	2026-09-04 10:54:09	Selamat! Anda lolos seleksi berkas administrasi. Silakan lanjut kerjakan ujian online yang tersedia pada menu Riwayat Lamaran.	pending	\N	\N	\N	pending	\N	\N	\N
7	3	3	Reviewed	2026-09-09 20:10:41	Selamat! Anda lolos seleksi berkas administrasi. Silakan lanjut kerjakan ujian online yang tersedia pada menu Riwayat Lamaran.	pending	\N	\N	\N	pending	\N	\N	\N
8	5	2	Submitted	2026-09-29 08:31:25	\N	pending	\N	\N	\N	pending	\N	\N	\N
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: job_degrees; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.job_degrees (id, job_id, degree_id) FROM stdin;
\.


--
-- Data for Name: job_majors; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.job_majors (id, job_id, major_id) FROM stdin;
\.


--
-- Data for Name: job_test; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.job_test (id, test_id, job_id, created_at, updated_at) FROM stdin;
1	1	3	2026-09-04 08:13:47	2026-09-04 08:13:47
2	2	1	2026-09-04 08:13:48	2026-09-04 08:13:48
3	3	2	2026-09-04 08:13:48	2026-09-04 08:13:48
4	4	4	2026-09-04 08:13:48	2026-09-04 08:13:48
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.jobs (id, company_id, department_id, title, description, employment_type, location, salary_min, salary_max, quota, deadline, status, position_id, reviewer_id) FROM stdin;
2	2	4	IoT Engineer	<p>Pengalaman dengan ESP32 dan Arduino.</p>	Contract	Semarang	6000000.00	10000000.00	1	2026-10-31	Open	3	\N
3	3	4	Quality Assurance (QA)	<p><strong>KUALIFIKASI</strong></p><ul><li>D3/S1 Analis Kesehatan, Analis Kimia, Kimia Murni, Teknik Kimia atau relevan;</li><li>Memiliki kemampuan komunikasi yang baik;</li><li>Cekatan &amp; Jujur;</li><li>Mahir Berbahasa Inggris;</li><li>Bersedia mobile working;</li></ul>	Full-time	Semarang	3400000.00	3700000.00	5	2026-10-01	Open	4	\N
1	4	5	Sales Marketing	<p>Menguasai React JS, TailwindCSS.</p>	Internship	Semarang	5000000.00	8000000.00	2	2026-09-25	Open	9	\N
4	1	2	Brand Manager	<p><strong>Syarat Pendidikan</strong></p><ul><li>Minimal lulusan <strong>S1 (Sarjana)</strong>.</li><li>Jurusan yang disukai: <strong>Pemasaran (Marketing)</strong>, Bisnis, Komunikasi, Manajemen, atau bidang terkait lainnya.</li><li>Gelar pascasarjana (<strong>MBA</strong>) atau sertifikasi strategi merek menjadi nilai tambah yang disukai perusahaan besar. </li></ul><p><br></p><p><strong>Syarat Pengalaman Kerja</strong></p><ul><li>Memiliki pengalaman kerja minimal <strong>3 hingga 5 tahun</strong> di bidang pemasaran, branding, atau manajemen merek.</li><li>Berpengalaman dalam memimpin kampanye pemasaran, peluncuran produk baru, serta bekerja sama dengan agensi eksternal atau tim lintas fungsi. </li></ul><p><br></p><p><strong>Keterampilan dan Kompetensi (Skill) yang Dibutuhkan</strong></p><ul><li><strong>Kemampuan Analitis:</strong> Mampu mengolah data pasar, memahami perilaku konsumen, dan membaca tren industri.</li><li><strong>Kepemimpinan:</strong> Memiliki jiwa kepemimpinan yang kuat karena peran ini menuntut koordinasi lintas tim layaknya seorang pemimpin perusahaan kecil (<em>mini CEO</em>).</li></ul><p><br></p>	Full-time	Semarang	2000000.00	8000000.00	1	2026-09-30	Open	8	\N
5	1	2	PS Manufaktur	<p><strong>Syarat Umum &amp; Administratif:</strong></p><ul><li><strong>Usia:</strong> Minimal <strong>18 tahun</strong> (bagi warga negara Indonesia).</li><li><strong>Bahasa:</strong> Lulus ujian kemampuan bahasa Jepang minimal level <strong>JLPT N4</strong> atau <strong>JFT-Basic A2</strong>.</li><li><strong>Kesehatan:</strong> Sehat jasmani dan rohani serta lolos tes medis.</li></ul><p><br></p>	Full-time	Semarang	2000000.00	4000000.00	2	2026-10-15	Open	10	\N
\.


--
-- Data for Name: languages; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.languages (id, profile_id, name, certificate_path) FROM stdin;
\.


--
-- Data for Name: majors; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.majors (id, name, created_at, updated_at) FROM stdin;
1	Teknik Informatika	2026-08-26 10:42:46	2026-08-26 10:42:46
2	Sistem Informasi	2026-08-26 10:42:46	2026-08-26 10:42:46
3	Teknik Komputer	2026-08-26 10:42:46	2026-08-26 10:42:46
4	Teknik Elektro	2026-08-26 10:42:46	2026-08-26 10:42:46
5	Manajemen	2026-08-26 10:42:46	2026-08-26 10:42:46
6	Akuntansi	2026-08-26 10:42:46	2026-08-26 10:42:46
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_roles_table	1
2	0001_01_01_000001_create_users_table	1
3	0001_01_01_000002_create_cache_table	1
4	0001_01_01_000003_create_jobs_table	1
5	2026_08_04_000000_create_companies_departments_jobs_tables	1
6	2026_08_04_000001_create_applicant_profiles_and_related_tables	1
7	2026_08_04_000002_create_transactional_tables	1
8	2026_08_07_000000_create_degrees_majors_and_job_education_relations	1
9	2026_08_07_010000_create_online_test_tables	1
10	2026_08_12_000000_create_disc_tables	1
11	2026_08_12_010000_change_gpa_column_in_educations_table	1
12	2026_08_13_000000_create_applicant_job_preferences_and_families_tables	1
13	2026_08_13_010000_add_family_fields_to_applicant_profile_table	1
14	2026_08_13_020000_add_unique_to_applicant_profile_user_id	1
15	2026_08_14_000000_add_cv_file_path_to_applicant_profile_table	1
16	2026_08_19_012935_add_attachment_fields_to_test_answers_table	1
17	2026_08_24_000000_add_suitable_jobs_to_disc_profiles_table	1
18	2026_08_26_000001_add_most_least_tags_to_question_options_table	2
19	2026_08_31_000001_create_employee_profiles_and_seed_role	3
20	2026_08_31_000002_make_tests_and_test_attempts_flexible_for_employees	3
21	2026_08_31_000003_add_photo_to_employee_profiles_table	3
22	2026_08_31_000004_add_participant_info_to_test_attempts_table	3
23	2026_09_02_000001_add_employee_type_to_employee_profiles_table	4
24	2026_09_02_000002_add_target_employee_type_to_tests_table	4
25	2026_09_03_000001_create_department_test_pivot_table	5
26	2026_09_03_000002_create_job_test_pivot_table	5
27	2026_09_03_000003_create_positions_table	5
28	2026_09_03_000004_add_position_id_to_jobs_and_employee_profiles_table	5
29	2026_09_04_000001_add_google_id_and_make_fields_nullable_in_users_table	6
30	2026_09_07_000001_add_about_fields_to_companies_table	7
31	2026_09_22_000001_create_papi_kostick_tables	8
32	2026_09_28_084236_add_double_approval_and_recruiter_toggle_fields	9
33	2026_09_29_000001_create_company_showcases_table	10
\.


--
-- Data for Name: organizations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.organizations (id, profile_id, name, "position", description, is_active, start_month, start_year, end_month, end_year) FROM stdin;
\.


--
-- Data for Name: papi_aspects; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.papi_aspects (id, name, english_name, order_number, created_at, updated_at) FROM stdin;
1	Arah Kerja	Work Direction	1	2026-09-24 08:19:15	2026-09-24 08:19:15
2	Kepemimpinan	Leadership	2	2026-09-24 08:19:15	2026-09-24 08:19:15
3	Aktivitas	Activity	3	2026-09-24 08:19:15	2026-09-24 08:19:15
4	Pergaulan	Social Nature	4	2026-09-24 08:19:15	2026-09-24 08:19:15
5	Gaya Kerja	Work Style	5	2026-09-24 08:19:15	2026-09-24 08:19:15
6	Sifat	Temperament	6	2026-09-24 08:19:15	2026-09-24 08:19:15
7	Ketaatan	Followership	7	2026-09-24 08:19:15	2026-09-24 08:19:15
\.


--
-- Data for Name: papi_factors; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.papi_factors (id, aspect_id, code, name, english_name, type, description, created_at, updated_at) FROM stdin;
1	1	N	Penyelesaian secara prestasi	Need to finish task	need	Mengukur kebutuhan untuk menyelesaikan tugas secara tuntas dan mandiri dengan standar hasil kerja yang baik.	2026-09-24 08:19:15	2026-09-24 08:19:15
2	1	G	Peranan sebagai pekerja keras	Hard intense worked	role	Mengukur persepsi individu sebagai pekerja keras yang mencurahkan tenaga, ketekunan, dan dedikasi dalam bekerja.	2026-09-24 08:19:15	2026-09-24 08:19:15
3	1	A	Hasrat untuk berprestasi	Need to achieve	need	Mengukur ambisi pribadi dan dorongan untuk mencapai sukses, keunggulan, serta standar prestasi tinggi.	2026-09-24 08:19:15	2026-09-24 08:19:15
4	2	L	Peran sebagai pimpinan	Leadership role	role	Tingkat dimana seseorang memproyeksikan dirinya sebagai pemimpin dan menggunakan orang lain untuk mencapai tujuan.	2026-09-24 08:19:15	2026-09-24 08:19:15
5	2	P	Pengendalian orang lain	Need to control others	need	Kebutuhan untuk menerima tanggung jawab atas pekerjaan dan tindakan orang lain serta mengatur bawahan.	2026-09-24 08:19:15	2026-09-24 08:19:15
6	2	I	Mudah dalam mengambil keputusan	Ease in decision making	role	Peran dan kelancaran individu dalam membuat keputusan, mulai dari berhati-hati hingga berani dan tegas.	2026-09-24 08:19:15	2026-09-24 08:19:15
7	3	T	Tipe selalu sibuk	Pace	role	Mengukur tempo kerja dan keaktifan internal/mental dalam menangani berbagai aktivitas pekerjaan.	2026-09-24 08:19:15	2026-09-24 08:19:15
8	3	V	Tipe yang bersemangat	Vigorous type	role	Mengukur energi fisik, stamina, keaktifan gerak, dan semangat dinamis dalam bekerja.	2026-09-24 08:19:15	2026-09-24 08:19:15
9	4	X	Kebutuhan untuk mendapatkan perhatian	Need to be noticed	need	Mengukur kebutuhan untuk dikenal, diapresiasi, dan mendapatkan perhatian nyata dari orang lain.	2026-09-24 08:19:15	2026-09-24 08:19:15
10	4	S	Pergaulan luas	Social extension	role	Tingkat kepercayaan dalam relasi interpersonal dan minat terhadap interaksi sosial kemasyarakatan.	2026-09-24 08:19:15	2026-09-24 08:19:15
11	4	B	Kebutuhan berkelompok	Need to belong to groups	need	Kebutuhan untuk menjadi bagian dari kelompok dan rasa keterikatan/penerimaan dalam tim.	2026-09-24 08:19:15	2026-09-24 08:19:15
12	4	O	Kebutuhan untuk dekat dan menyayangi	Need for closeness and affection	need	Kebutuhan akan kehangatan hubungan antarpribadi dan kedekatan emosional di lingkungan kerja.	2026-09-24 08:19:15	2026-09-24 08:19:15
13	5	R	Tipe teoritikal	Theoretical type	role	Kecenderungan berpikir konseptual, analitis, penalaran logis, dan pertimbangan teoritis.	2026-09-24 08:19:15	2026-09-24 08:19:15
14	5	D	Suka pekerjaan yang terperinci	Interest in working with details	role	Minat dan ketelitian dalam menangani hal-hal detail, spesifik, dan memerlukan kecermatan tinggi.	2026-09-24 08:19:15	2026-09-24 08:19:15
15	5	C	Tipe teratur	Organized type	role	Tingkat keteraturan, kerapian, metode kerja sistematis, dan ketaatan terhadap struktur kerja.	2026-09-24 08:19:15	2026-09-24 08:19:15
16	6	Z	Hasrat untuk berubah	Need for change	need	Kebutuhan akan variasi, hal baru, inovasi, fleksibilitas lingkungan, serta adaptasi perubahan.	2026-09-24 08:19:15	2026-09-24 08:19:15
17	6	E	Pengendalian emosi	Emotional resistant	role	Pengendalian perasaan, kestabilan emosi, ketenangan di bawah tekanan, serta pengekangan diri.	2026-09-24 08:19:15	2026-09-24 08:19:15
18	6	K	Agresi	Need to be forceful	need	Dorongan bersaing, ketegasan pendirian, agresi positif dalam pekerjaan, dan penanganan konflik.	2026-09-24 08:19:15	2026-09-24 08:19:15
19	7	F	Dukungan terhadap atasan	Need to support authority	need	Kebutuhan untuk loyal, mendukung, dan membantu figur otoritas/atasan secara pribadi maupun profesional.	2026-09-24 08:19:15	2026-09-24 08:19:15
20	7	W	Kebutuhan taat pada aturan dan pengarahan	Need for rules and supervision	need	Kebutuhan akan aturan baku, kepatuhan prosedur kerja, dan pengarahan serta supervisi dari manajemen.	2026-09-24 08:19:15	2026-09-24 08:19:15
\.


--
-- Data for Name: papi_norms; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.papi_norms (id, factor_id, factor_code, min_score, max_score, interpretation, description, created_at, updated_at) FROM stdin;
1	1	N	0	2	Menunda atau menghindari pekerjaan	Menunda atau menghindari pekerjaan	2026-09-25 10:13:12	2026-09-25 10:13:12
2	1	N	3	4	Cenderung ragu-ragu dalam situasi pengambilan keputusan, cenderung ragu-ragu, menunda atau menghindari situasi pengambilan keputusan	Berhati-hati atau ragu	2026-09-25 10:13:12	2026-09-25 10:13:12
3	1	N	4	6	Cukup bertanggung jawab terhadap pekerjaan	Cukup bertanggung jawab pada pekerjaan	2026-09-25 10:13:12	2026-09-25 10:13:12
4	1	N	6	9	Ketekunan, tanggung jawab terhadap tugas tinggi	Tekun, tanggung jawab tinggi	2026-09-25 10:13:12	2026-09-25 10:13:12
5	3	A	0	5	Mencerminkan ketidakpastian tujuan. Juga mencerminkan kepuasan dalam suatu pekerjaan, tidak perlu melanjutkan usaha untuk sukses	Ketidakpastian tujuan, kepuasan dalam suatu pekerjaan, tidak ada usaha lebih	2026-09-25 10:13:12	2026-09-25 10:13:12
6	3	A	6	9	Tujuan-tujuan didefinisikan secara jelas, kebutuhan untuk sukses tinggi, ambisi pribadi tinggi	Tujuan jelas, kebutuhan sukses dan ambisi tinggi	2026-09-25 10:13:12	2026-09-25 10:13:12
7	2	G	3	4	Bekerja hanya untuk mengejar kesenangan saja bukan untuk memberikan suatu hasil yang baik	Bekerja untuk kesenangan saja, bukan hasil optimal	2026-09-25 10:13:12	2026-09-25 10:13:12
8	2	G	4	7	Kemauan bekerja keras tinggi	Kemauan bekerja keras tinggi	2026-09-25 10:13:12	2026-09-25 10:13:12
9	4	L	0	4	Cenderung tidak suka aktif menggunakan orang lain dalam bekerja	Cenderung tidak secara aktif menggunakan orang lain dalam bekerja	2026-09-25 10:13:12	2026-09-25 10:13:12
10	4	L	5	9	Yaitu tingkat dimana seseorang memproyeksikan dirinya sebagai pemimpin suatu tingkat, dimana ia mencoba menggunakan orang lain untuk mencapai tujuannya. Nilai S menunjukkan apakah pola kepemimpinannya bersifat persuasive, demokratis, atau otoriter	Memproyeksikan diri sebagai pemimpin, menggunakan orang lain untuk mencapai tujuan	2026-09-25 10:13:12	2026-09-25 10:13:12
11	5	P	0	4	Menurunnya keinginan untuk bertanggung jawab terhadap pekerjaan dan tindakan orang lain	Menurunnya keinginan untuk bertanggung jawab pada pekerjaan dan tindakan orang lain	2026-09-25 10:13:12	2026-09-25 10:13:12
12	5	P	5	9	Tingkat kebutuhan untuk menerima tanggung jawab orang lain, menjadi orang yang bertanggung jawab	Tingkat kebutuhan untuk menerima tanggung jawab orang lain, menjadi orang yang bertanggung jawab	2026-09-25 10:13:12	2026-09-25 10:13:12
13	6	I	0	2	Ragu-ragu sampai penundaan/menolak situasi pengambilan keputusan	Ragu – menolak mengambil keputusan	2026-09-25 10:13:12	2026-09-25 10:13:12
14	6	I	3	4	Berhati-hati sampai ragu-ragu dalam membuat keputusan	Berhati-hati membuat keputusan	2026-09-25 10:13:12	2026-09-25 10:13:12
15	6	I	5	7	Mudah dan lancar sampai berhati-hati dalam membuat keputusan	Berhati-hati – lancar dan mudah mengambil keputusan	2026-09-25 10:13:12	2026-09-25 10:13:12
16	6	I	8	9	Tidak ragu-ragu dalam proses pengambilan keputusan	Tidak ragu dalam mengambil keputusan	2026-09-25 10:13:12	2026-09-25 10:13:12
17	7	T	0	3	Melakukan segala sesuatu menurut kemauannya sendiri	Melakukan segala sesuatu menurut kemauannya sendiri	2026-09-25 10:13:12	2026-09-25 10:13:12
18	7	T	4	6	Tergolong aktif secara internal dan mental	Tergolong aktif secara internal dan mental	2026-09-25 10:13:12	2026-09-25 10:13:12
19	8	V	0	4	Keaktifannya tergolong rendah, cenderung pasif (hanya duduk-duduk saja)	Cenderung pasif	2026-09-25 10:13:12	2026-09-25 10:13:12
20	8	V	5	7	Keaktifannya secara fisik tergolong agak baik, cenderung tipe sportif	Aktif secara fisik, cenderung sportif	2026-09-25 10:13:12	2026-09-25 10:13:12
21	9	X	0	1	Cenderung pemalu, suka menyendiri	Cenderung pemalu	2026-09-25 10:13:12	2026-09-25 10:13:12
22	9	X	2	3	Rendah hati, tulus	Rendah hati, tulus	2026-09-25 10:13:12	2026-09-25 10:13:12
23	9	X	4	5	Khusus, memiliki pola yang nyata	Memiliki pola perilaku yang unik	2026-09-25 10:13:12	2026-09-25 10:13:12
24	9	X	6	9	Membutuhkan perhatian yang nyata	Membutuhkan perhatian nyata	2026-09-25 10:13:12	2026-09-25 10:13:12
25	10	S	0	5	Memiliki penilaian yang rendah terhadap hubungan sosial, cenderung kurang percaya pada orang lain	Perhatian rendah terhadap hubungan sosial, kurang percaya pada orang lain	2026-09-25 10:13:12	2026-09-25 10:13:12
26	10	S	6	9	Tingkat kepercayaan dalam hubungan sosial tinggi, menyukai interaksi sosial	Kepercayaan tinggi dalam hubungan sosial, suka interaksi sosial	2026-09-25 10:13:12	2026-09-25 10:13:12
27	11	B	0	3	Selektif, secara umum melepaskan diri dari kelompok	Selektif	2026-09-25 10:13:12	2026-09-25 10:13:12
28	11	B	4	5	Ada kebutuhan untuk diterima dan diakui tetapi tidak terlalu mudah dipengaruhi oleh kelompok	Butuh diterima, tapi tidak mudah dipengaruhi kelompok	2026-09-25 10:13:12	2026-09-25 10:13:12
29	11	B	6	9	Kebutuhan untuk disukai, diakui oleh semua orang. Mudah dipengaruhi kelompok	Butuh disukai dan diakui, mudah dipengaruhi	2026-09-25 10:13:12	2026-09-25 10:13:12
30	12	O	0	2	Tidak menyukai hubungan antar pribadi. Tidak menyukai interaksi perseorangan	Tidak suka hubungan perorangan	2026-09-25 10:13:12	2026-09-25 10:13:12
31	12	O	3	4	Sadar akan kebutuhan antar pribadi tetapi dapat melepaskan diri dari orang lain/tidak terlalu tergantung	Sadar akan hubungan perorangan, tapi tidak terlalu tergantung	2026-09-25 10:13:12	2026-09-25 10:13:12
32	12	O	5	9	Ketergantungan yang sangat besar akan pengakuan dan penerimaan diri	Sangat tergantung, butuh penerimaan diri	2026-09-25 10:13:12	2026-09-25 10:13:12
33	13	R	0	4	Kurang perhatian-praktis	Kurang perhatian, bersifat praktis	2026-09-25 10:13:12	2026-09-25 10:13:12
34	13	R	5	9	Penekanan pada nilai-nilai penalaran tergolong tinggi	Nilai-nilai penalaran tergolong tinggi	2026-09-25 10:13:12	2026-09-25 10:13:12
35	14	D	0	3	Menyadari kebutuhan akan kecermatan tetapi secara pribadi tidak berminat menangani hal-hal detail	Menyadari kebutuhan akan kecermatan, tetapi tidak berminat bekerja detail	2026-09-25 10:13:12	2026-09-25 10:13:12
36	14	D	4	9	Minat menangani hal-hal detail tergolong tinggi	Minat tinggi untuk bekerja secara detail	2026-09-25 10:13:12	2026-09-25 10:13:12
37	15	C	0	2	Fleksibilitas sampai ketidak-teraturan	Fleksibel – tidak teratur	2026-09-25 10:13:12	2026-09-25 10:13:12
38	15	C	3	5	Tergolong teratur tetapi dengan fleksibilitas	Teratur tetapi tidak tergolong fleksibel	2026-09-25 10:13:12	2026-09-25 10:13:12
39	15	C	6	9	Memiliki keteraturan yang sangat tinggi, cenderung kaku	Keteraturan tinggi cenderung kaku	2026-09-25 10:13:12	2026-09-25 10:13:12
40	16	Z	0	2	Tidak menyukai dan menolak perubahan. Cenderung menggunakan pendekatan-pendekatan tradisional	Tidak suka berubah	2026-09-25 10:13:12	2026-09-25 10:13:12
41	16	Z	3	4	Tidak suka akan perubahan jika dipaksakan kepadanya	Tidak suka perubahan jika dipaksakan	2026-09-25 10:13:12	2026-09-25 10:13:12
42	16	Z	5	6	Mudah menyesuaikan diri	Mudah menyesuaikan diri	2026-09-25 10:13:12	2026-09-25 10:13:12
43	16	Z	6	7	Pembuat perubahan yang selektif. Berpikir jauh ke depan	Membuat perubahan yang selektif, berfikir jauh ke depan	2026-09-25 10:13:12	2026-09-25 10:13:12
44	16	Z	8	9	Mudah gelisah, mudah frustrasi mungkin karena segala sesuatu bergerak tidak cukup cepat	Mudah gelisah, frustasi, karena segala sesuatu tidak berjalan fantastis	2026-09-25 10:13:12	2026-09-25 10:13:12
45	17	E	0	1	Terbuka , cepat bereaksi , tidak memikirkan nilai dalam pengendalian diri	Terbuka, cepat bereaksi, tidak normative	2026-09-25 10:13:12	2026-09-25 10:13:12
46	17	E	2	3	Terbuka	Terbuka	2026-09-25 10:13:12	2026-09-25 10:13:12
47	17	E	4	6	Memiliki pendekatan emosional yang seimbang. Mampu mengendalikan perasaannya	Punya pendekatan emosional seimbang, mampu mengendalikan	2026-09-25 10:13:12	2026-09-25 10:13:12
48	17	E	7	9	Sangat normative , kebutuhan pengendalian diri yang berlebihan	Sangat normative, kebutuhan pengendalian diri yang berlebihan	2026-09-25 10:13:12	2026-09-25 10:13:12
49	18	K	0	2	Selalu menghindari masalah. Cenderung mengabaikan situasi atau cenderung menolak untuk mengenali sesuatu sebagai sebuah masalah	Menghindari masalah, menolak untuk mengenali situasi sebagai masalah	2026-09-25 10:13:12	2026-09-25 10:13:12
50	18	K	3	4	Lebih menyukai lingkungan yang tenang. Menghindari konflik. Cenderung menunda masalah	Suka lingkungan tenang, menghindari konflik	2026-09-25 10:13:12	2026-09-25 10:13:12
51	18	K	5	5	Kukuh pendirian, cenderung keras kepala	Keras kepala	2026-09-25 10:13:12	2026-09-25 10:13:12
52	18	K	6	7	Agresi pribadi yang berkaitan dengan pekerjaan, dorongan dan semangat bersaing	Agresi berhubungan dengan kerja, dorongan semangat bersaing	2026-09-25 10:13:12	2026-09-25 10:13:12
53	18	K	8	9	Agresif, cenderung defensive	Agresif, cenderung defensive	2026-09-25 10:13:12	2026-09-25 10:13:12
54	19	F	0	1	Cenderung egois, kemungkinan bisa bersikap memberontak	Cenderung egois, kemungkinan bisa memberontak	2026-09-25 10:13:12	2026-09-25 10:13:12
55	19	F	2	3	Mengurus kepentingan diri sendiri	Mengurus kepentingan sendiri	2026-09-25 10:13:12	2026-09-25 10:13:12
56	19	F	4	5	Setia terhadap perusahaan	Setia terhadap perusahaan	2026-09-25 10:13:12	2026-09-25 10:13:12
57	19	F	6	9	Bersikap setia dan membantu secara pribadi, ada kemungkinan bantuannya bermotivasi politis	Bersikap setia dan membantu, kemungkinan bantuannya bersifat politis	2026-09-25 10:13:12	2026-09-25 10:13:12
58	20	W	0	3	Berorientasi pada tujuan, mandiri	Berorientasi pada tujuan, mandiri	2026-09-25 10:13:12	2026-09-25 10:13:12
59	20	W	4	5	Kebutuhan akan pengarahan dan harapan yang dirumuskan untuknya	Kebutuhan akan pengarahan dan harapan yang dirumuskan untuknya	2026-09-25 10:13:12	2026-09-25 10:13:12
60	20	W	6	9	Meningkatnya orientasi terhadap tugas dan membutuhkan instruksi yang jelas	Meningkatnya orientasi terhadap tugas dan membutuhkan instruksi yang jelas	2026-09-25 10:13:12	2026-09-25 10:13:12
\.


--
-- Data for Name: papi_test_results; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.papi_test_results (id, test_attempt_id, scores, role_score, need_score, is_valid, interpretations, raw_answers, created_at, updated_at) FROM stdin;
1	18	{"N":5,"G":4,"A":5,"L":4,"P":5,"I":4,"T":5,"V":5,"X":4,"S":4,"B":4,"O":5,"R":4,"D":5,"C":5,"Z":5,"E":5,"K":4,"F":4,"W":4}	45	45	t	{"N":{"score":5,"interpretation":"Cukup bertanggung jawab terhadap pekerjaan","description":"Cukup bertanggung jawab pada pekerjaan"},"G":{"score":4,"interpretation":"Bekerja hanya untuk mengejar kesenangan saja bukan untuk memberikan suatu hasil yang baik","description":"Bekerja untuk kesenangan saja, bukan hasil optimal"},"A":{"score":5,"interpretation":"Mencerminkan ketidakpastian tujuan. Juga mencerminkan kepuasan dalam suatu pekerjaan, tidak perlu melanjutkan usaha untuk sukses","description":"Ketidakpastian tujuan, kepuasan dalam suatu pekerjaan, tidak ada usaha lebih"},"L":{"score":4,"interpretation":"Cenderung tidak suka aktif menggunakan orang lain dalam bekerja","description":"Cenderung tidak secara aktif menggunakan orang lain dalam bekerja"},"P":{"score":5,"interpretation":"Tingkat kebutuhan untuk menerima tanggung jawab orang lain, menjadi orang yang bertanggung jawab","description":"Tingkat kebutuhan untuk menerima tanggung jawab orang lain, menjadi orang yang bertanggung jawab"},"I":{"score":4,"interpretation":"Berhati-hati sampai ragu-ragu dalam membuat keputusan","description":"Berhati-hati membuat keputusan"},"T":{"score":5,"interpretation":"Tergolong aktif secara internal dan mental","description":"Tergolong aktif secara internal dan mental"},"V":{"score":5,"interpretation":"Keaktifannya secara fisik tergolong agak baik, cenderung tipe sportif","description":"Aktif secara fisik, cenderung sportif"},"X":{"score":4,"interpretation":"Khusus, memiliki pola yang nyata","description":"Memiliki pola perilaku yang unik"},"S":{"score":4,"interpretation":"Memiliki penilaian yang rendah terhadap hubungan sosial, cenderung kurang percaya pada orang lain","description":"Perhatian rendah terhadap hubungan sosial, kurang percaya pada orang lain"},"B":{"score":4,"interpretation":"Ada kebutuhan untuk diterima dan diakui tetapi tidak terlalu mudah dipengaruhi oleh kelompok","description":"Butuh diterima, tapi tidak mudah dipengaruhi kelompok"},"O":{"score":5,"interpretation":"Ketergantungan yang sangat besar akan pengakuan dan penerimaan diri","description":"Sangat tergantung, butuh penerimaan diri"},"R":{"score":4,"interpretation":"Kurang perhatian-praktis","description":"Kurang perhatian, bersifat praktis"},"D":{"score":5,"interpretation":"Minat menangani hal-hal detail tergolong tinggi","description":"Minat tinggi untuk bekerja secara detail"},"C":{"score":5,"interpretation":"Tergolong teratur tetapi dengan fleksibilitas","description":"Teratur tetapi tidak tergolong fleksibel"},"Z":{"score":5,"interpretation":"Mudah menyesuaikan diri","description":"Mudah menyesuaikan diri"},"E":{"score":5,"interpretation":"Memiliki pendekatan emosional yang seimbang. Mampu mengendalikan perasaannya","description":"Punya pendekatan emosional seimbang, mampu mengendalikan"},"K":{"score":4,"interpretation":"Lebih menyukai lingkungan yang tenang. Menghindari konflik. Cenderung menunda masalah","description":"Suka lingkungan tenang, menghindari konflik"},"F":{"score":4,"interpretation":"Setia terhadap perusahaan","description":"Setia terhadap perusahaan"},"W":{"score":4,"interpretation":"Kebutuhan akan pengarahan dan harapan yang dirumuskan untuknya","description":"Kebutuhan akan pengarahan dan harapan yang dirumuskan untuknya"}}	{"37":{"option_id":266,"tag":"P","choice":"b","text":"Saya ingin menjadi penanggung jawab bagi orang-orang lain"},"1":{"option_id":193,"tag":"G","choice":"a","text":"Saya seorang pekerja \\u201ckeras\\u201d"},"2":{"option_id":195,"tag":"A","choice":"a","text":"Saya suka bekerja lebih baik dari orang lain"},"3":{"option_id":197,"tag":"P","choice":"a","text":"Saya suka menunjukkan caranya melaksanakan sesuatu hal"},"4":{"option_id":199,"tag":"X","choice":"a","text":"Saya suka berkelakar"},"5":{"option_id":201,"tag":"B","choice":"a","text":"Saya suka menggabungkan diri dengan kelompok-kelompok"},"6":{"option_id":203,"tag":"O","choice":"a","text":"Saya senang bersahabat intim dengan seseorang"},"7":{"option_id":205,"tag":"Z","choice":"a","text":"Saya cepat berubah bila hal itu diperlukan"},"8":{"option_id":207,"tag":"K","choice":"a","text":"Saya suka \\u201cmembalas dendam\\u201d bila saya benar-benar disakiti"},"9":{"option_id":209,"tag":"F","choice":"a","text":"Saya ingin atasan saya menyukai saya"},"10":{"option_id":211,"tag":"W","choice":"a","text":"Saya suka mengikuti perintah-perintah yang diberikan kepada saya"},"11":{"option_id":213,"tag":"G","choice":"a","text":"Saya mencoba sekuat tenaga"},"12":{"option_id":215,"tag":"L","choice":"a","text":"Saya membuat orang lain melakukan apa yang saya inginkan"},"13":{"option_id":217,"tag":"P","choice":"a","text":"Saya suka mengatakan kepada kelompok, apa yang harus dilakukan"},"14":{"option_id":219,"tag":"X","choice":"a","text":"Saya ingin tampak bersemangat dan menarik"},"15":{"option_id":221,"tag":"B","choice":"a","text":"Saya suka menyelaraskan diri dengan kelompok"},"16":{"option_id":223,"tag":"O","choice":"a","text":"Saya cemas kalau orang lain tidak menyukai saya"},"17":{"option_id":225,"tag":"Z","choice":"a","text":"Saya suka mencoba sesuatu yang baru"},"18":{"option_id":227,"tag":"K","choice":"a","text":"Kadang-kadang saya menyalahkan orang lain bila terjadi sesuatu kesalahan"},"19":{"option_id":229,"tag":"F","choice":"a","text":"Saya suka menyenangkan hati orang yang memimpin saya"},"20":{"option_id":231,"tag":"W","choice":"a","text":"Saya menyukai petunjuk yang terinci untuk melakukan sesuatu pekerjaan"},"21":{"option_id":234,"tag":"D","choice":"b","text":"Saya senang bekerja dengan sangat cermat dan hati-hati"},"22":{"option_id":236,"tag":"C","choice":"b","text":"Saya mengorganisir tugas-tugas secara baik"},"23":{"option_id":238,"tag":"E","choice":"b","text":"Saya seorang yang lambat dalam membuat keputusan"},"24":{"option_id":240,"tag":"N","choice":"b","text":"Bila dalam kelompok, saya lebih suka diam"},"25":{"option_id":242,"tag":"A","choice":"b","text":"Saya ingin melakukan sesuatu lebih baik dari orang lain"},"26":{"option_id":244,"tag":"P","choice":"b","text":"Saya suka memberi nasihat kepada orang lain"},"27":{"option_id":246,"tag":"X","choice":"b","text":"Saya suka menceritakan keberhasilan saya dalam mengerjakan tugas"},"28":{"option_id":248,"tag":"B","choice":"b","text":"Saya suka bergabung ke dalam suatu kelompok"},"29":{"option_id":250,"tag":"O","choice":"b","text":"Saya berusaha untuk sangat intim dengan orang-orang"},"30":{"option_id":252,"tag":"Z","choice":"b","text":"Saya mudah merasa jemu (bosan)"},"31":{"option_id":254,"tag":"R","choice":"b","text":"Saya banyak berpikir dan berencana"},"32":{"option_id":256,"tag":"D","choice":"b","text":"Hal-hal yang kecil (detail) menarik hati saya"},"33":{"option_id":258,"tag":"C","choice":"b","text":"Saya meletakkan segala sesuatu secara rapi dan teratur"},"34":{"option_id":260,"tag":"E","choice":"b","text":"Saya jarang marah atau sedih"},"35":{"option_id":262,"tag":"N","choice":"b","text":"Pada suatu waktu tertentu, saya hanya ingin mengerjakan satu tugas saja"},"36":{"option_id":264,"tag":"A","choice":"b","text":"Saya berusaha keras untuk menjadi yang terbaik"},"38":{"option_id":268,"tag":"X","choice":"b","text":"Saya ingin diperhatikan"},"39":{"option_id":270,"tag":"B","choice":"b","text":"Saya tertarik menjadi anggota dari suatu kelompok"},"40":{"option_id":272,"tag":"O","choice":"b","text":"Saya suka orang-orang mengenal saya benar-benar"},"41":{"option_id":273,"tag":"G","choice":"a","text":"Saya mencoba sekuat tenaga"},"42":{"option_id":275,"tag":"L","choice":"a","text":"Orang lain beranggapan bahwa saya adalah seorang pemimpin yang baik"},"43":{"option_id":277,"tag":"I","choice":"a","text":"Seringkali saya memanfaatkan peluang"},"44":{"option_id":279,"tag":"T","choice":"a","text":"Orang lain menganggap saya bekerja cepat"},"45":{"option_id":281,"tag":"V","choice":"a","text":"Saya menyukai permainan-permainan dan olahraga"},"46":{"option_id":283,"tag":"O","choice":"a","text":"Saya senang bila orang-orang dapat intim dan bersahabat"},"47":{"option_id":285,"tag":"Z","choice":"a","text":"Saya suka bereksperimen dan mencoba sesuatu yang baru"},"48":{"option_id":287,"tag":"K","choice":"a","text":"Saya senang diperlakukan secara adil"},"49":{"option_id":289,"tag":"F","choice":"a","text":"Saya suka mengerjakan apa yang diharapkan dari saya"},"50":{"option_id":291,"tag":"W","choice":"a","text":"Saya suka petunjuk-petunjuk terinci dalam melaksanakan pekerjaan"},"51":{"option_id":293,"tag":"G","choice":"a","text":"Saya selalu berusaha mengerjakan tugas secara sempurna"},"52":{"option_id":295,"tag":"L","choice":"a","text":"Saya tergolong tipe pemimpin"},"58":{"option_id":307,"tag":"K","choice":"a","text":"Biasanya saya bersikeras mengenai apa yang saya yakini"},"53":{"option_id":297,"tag":"I","choice":"a","text":"Saya memanfaatkan peluang-peluang"},"54":{"option_id":299,"tag":"T","choice":"a","text":"Saya bekerja dengan kecepatan yang mantap dan cepat"},"55":{"option_id":301,"tag":"V","choice":"a","text":"Saya memiliki banyak energi untuk permainan-permainan dan olahraga"},"56":{"option_id":303,"tag":"S","choice":"a","text":"Saya bergaul baik dengan semua orang"},"57":{"option_id":305,"tag":"Z","choice":"a","text":"Saya ingin berkenalan dengan orang-orang baru dan mengerjakan hal baru"},"59":{"option_id":309,"tag":"F","choice":"a","text":"Saya menyukai saran-saran dari orang-orang yang saya kagumi"},"60":{"option_id":311,"tag":"W","choice":"a","text":"Saya biarkan orang-orang lain mempengaruhi saya"},"61":{"option_id":314,"tag":"T","choice":"b","text":"Biasanya saya bekerja cepat"},"62":{"option_id":316,"tag":"V","choice":"b","text":"Saya terampil mempergunakan alat-alat kerja"},"63":{"option_id":318,"tag":"S","choice":"b","text":"Saya lambat dalam mengambil keputusan"},"64":{"option_id":320,"tag":"R","choice":"b","text":"Saya suka membaca"},"65":{"option_id":322,"tag":"D","choice":"b","text":"Saya menyukai pekerjaan yang harus dilakukan secara teliti"},"66":{"option_id":324,"tag":"C","choice":"b","text":"Saya dapat menemukan hal-hal yang telah saya pindahkan"},"67":{"option_id":326,"tag":"E","choice":"b","text":"Saya selalu menyenangkan"},"68":{"option_id":328,"tag":"N","choice":"b","text":"Saya tetap menekuni satu permasalahan sampai ia terselesaikan"},"69":{"option_id":330,"tag":"A","choice":"b","text":"Saya suka menjadi seorang yang berhasil"},"70":{"option_id":332,"tag":"P","choice":"b","text":"Saya suka mengambil keputusan untuk kelompok"},"71":{"option_id":334,"tag":"I","choice":"b","text":"Saya cepat dan mudah mengambil keputusan"},"72":{"option_id":336,"tag":"T","choice":"b","text":"Biasanya saya tergesa-gesa"},"73":{"option_id":338,"tag":"V","choice":"b","text":"Saya lambat di dalam mengambil keputusan"},"74":{"option_id":340,"tag":"S","choice":"b","text":"Saya mudah mendapat kawan"},"75":{"option_id":342,"tag":"R","choice":"b","text":"Sebagian besar waktu saya untuk berpikir"},"76":{"option_id":344,"tag":"D","choice":"b","text":"Saya menyukai pekerjaan yang menuntut ketepatan"},"77":{"option_id":346,"tag":"C","choice":"b","text":"Saya meletakkan segala sesuatu pada tempatnya"},"78":{"option_id":348,"tag":"E","choice":"b","text":"Saya tidak cepat marah"},"79":{"option_id":350,"tag":"N","choice":"b","text":"Saya selalu menyelesaikan pekerjaan yang saya mulai"},"80":{"option_id":352,"tag":"A","choice":"b","text":"Saya suka bekerja \\u201ckeras\\u201d"},"81":{"option_id":354,"tag":"L","choice":"b","text":"Saya adalah seorang pemimpin yang baik"},"82":{"option_id":356,"tag":"I","choice":"b","text":"Saya adalah seorang yang \\u201cgampangan\\u201d (tak banyak pertimbangan)"},"83":{"option_id":358,"tag":"T","choice":"b","text":"Bicara saya cepat"},"84":{"option_id":360,"tag":"V","choice":"b","text":"Secara teratur saya berolahraga"},"85":{"option_id":362,"tag":"S","choice":"b","text":"Saya cepat lelah"},"86":{"option_id":364,"tag":"R","choice":"b","text":"Banyak waktu saya untuk berpikir"},"87":{"option_id":366,"tag":"D","choice":"b","text":"Saya suka bekerja sedetil-detilnya"},"88":{"option_id":368,"tag":"C","choice":"b","text":"Saya suka mengorganisir pekerjaan saya"},"89":{"option_id":370,"tag":"E","choice":"b","text":"Saya selalu menyenangkan"},"90":{"option_id":372,"tag":"N","choice":"b","text":"Saya harus menyelesaikan apa yang sudah saya mulai"}}	2026-09-24 08:58:24	2026-09-24 08:58:24
2	19	{"N":3,"G":8,"A":4,"L":4,"P":4,"I":1,"T":6,"V":4,"X":6,"S":4,"B":3,"O":6,"R":4,"D":2,"C":7,"Z":6,"E":5,"K":5,"F":6,"W":2}	45	45	t	{"N":{"score":3,"interpretation":"Cenderung ragu-ragu dalam situasi pengambilan keputusan, cenderung ragu-ragu, menunda atau menghindari situasi pengambilan keputusan","description":"Berhati-hati atau ragu"},"G":{"score":8,"interpretation":"Kemauan bekerja keras tinggi","description":"Kemauan bekerja keras tinggi"},"A":{"score":4,"interpretation":"Mencerminkan ketidakpastian tujuan. Juga mencerminkan kepuasan dalam suatu pekerjaan, tidak perlu melanjutkan usaha untuk sukses","description":"Ketidakpastian tujuan, kepuasan dalam suatu pekerjaan, tidak ada usaha lebih"},"L":{"score":4,"interpretation":"Cenderung tidak suka aktif menggunakan orang lain dalam bekerja","description":"Cenderung tidak secara aktif menggunakan orang lain dalam bekerja"},"P":{"score":4,"interpretation":"Menurunnya keinginan untuk bertanggung jawab terhadap pekerjaan dan tindakan orang lain","description":"Menurunnya keinginan untuk bertanggung jawab pada pekerjaan dan tindakan orang lain"},"I":{"score":1,"interpretation":"Ragu-ragu sampai penundaan\\/menolak situasi pengambilan keputusan","description":"Ragu \\u2013 menolak mengambil keputusan"},"T":{"score":6,"interpretation":"Tergolong aktif secara internal dan mental","description":"Tergolong aktif secara internal dan mental"},"V":{"score":4,"interpretation":"Keaktifannya tergolong rendah, cenderung pasif (hanya duduk-duduk saja)","description":"Cenderung pasif"},"X":{"score":6,"interpretation":"Membutuhkan perhatian yang nyata","description":"Membutuhkan perhatian nyata"},"S":{"score":4,"interpretation":"Memiliki penilaian yang rendah terhadap hubungan sosial, cenderung kurang percaya pada orang lain","description":"Perhatian rendah terhadap hubungan sosial, kurang percaya pada orang lain"},"B":{"score":3,"interpretation":"Selektif, secara umum melepaskan diri dari kelompok","description":"Selektif"},"O":{"score":6,"interpretation":"Ketergantungan yang sangat besar akan pengakuan dan penerimaan diri","description":"Sangat tergantung, butuh penerimaan diri"},"R":{"score":4,"interpretation":"Kurang perhatian-praktis","description":"Kurang perhatian, bersifat praktis"},"D":{"score":2,"interpretation":"Menyadari kebutuhan akan kecermatan tetapi secara pribadi tidak berminat menangani hal-hal detail","description":"Menyadari kebutuhan akan kecermatan, tetapi tidak berminat bekerja detail"},"C":{"score":7,"interpretation":"Memiliki keteraturan yang sangat tinggi, cenderung kaku","description":"Keteraturan tinggi cenderung kaku"},"Z":{"score":6,"interpretation":"Mudah menyesuaikan diri","description":"Mudah menyesuaikan diri"},"E":{"score":5,"interpretation":"Memiliki pendekatan emosional yang seimbang. Mampu mengendalikan perasaannya","description":"Punya pendekatan emosional seimbang, mampu mengendalikan"},"K":{"score":5,"interpretation":"Kukuh pendirian, cenderung keras kepala","description":"Keras kepala"},"F":{"score":6,"interpretation":"Bersikap setia dan membantu secara pribadi, ada kemungkinan bantuannya bermotivasi politis","description":"Bersikap setia dan membantu, kemungkinan bantuannya bersifat politis"},"W":{"score":2,"interpretation":"Berorientasi pada tujuan, mandiri","description":"Berorientasi pada tujuan, mandiri"}}	{"1":{"option_id":193,"tag":"G","choice":"a","text":"Saya seorang pekerja \\u201ckeras\\u201d"},"2":{"option_id":195,"tag":"A","choice":"a","text":"Saya suka bekerja lebih baik dari orang lain"},"3":{"option_id":198,"tag":"A","choice":"b","text":"Saya ingin bekerja sebaik mungkin"},"4":{"option_id":200,"tag":"P","choice":"b","text":"Saya senang mengatakan kepada orang lain, apa yang harus dilakukannya"},"5":{"option_id":201,"tag":"B","choice":"a","text":"Saya suka menggabungkan diri dengan kelompok-kelompok"},"6":{"option_id":203,"tag":"O","choice":"a","text":"Saya senang bersahabat intim dengan seseorang"},"7":{"option_id":206,"tag":"O","choice":"b","text":"Saya berusaha untuk intim dengan teman-teman"},"8":{"option_id":208,"tag":"Z","choice":"b","text":"Saya suka melakukan hal-hal yang baru dan berbeda"},"9":{"option_id":209,"tag":"F","choice":"a","text":"Saya ingin atasan saya menyukai saya"},"10":{"option_id":212,"tag":"F","choice":"b","text":"Saya suka menyenangkan hati orang yang memimpin saya"},"11":{"option_id":214,"tag":"C","choice":"b","text":"Saya seorang yang tertib. Saya meletakkan segala sesuatu pada tempatnya"},"12":{"option_id":215,"tag":"L","choice":"a","text":"Saya membuat orang lain melakukan apa yang saya inginkan"},"13":{"option_id":218,"tag":"N","choice":"b","text":"Saya menekuni satu pekerjaan sampai selesai"},"14":{"option_id":219,"tag":"X","choice":"a","text":"Saya ingin tampak bersemangat dan menarik"},"15":{"option_id":221,"tag":"B","choice":"a","text":"Saya suka menyelaraskan diri dengan kelompok"},"16":{"option_id":224,"tag":"X","choice":"b","text":"Saya senang kalau orang-orang memperhatikan saya"},"17":{"option_id":225,"tag":"Z","choice":"a","text":"Saya suka mencoba sesuatu yang baru"},"18":{"option_id":228,"tag":"O","choice":"b","text":"Saya cemas bila seseorang tidak menyukai saya"},"19":{"option_id":229,"tag":"F","choice":"a","text":"Saya suka menyenangkan hati orang yang memimpin saya"},"20":{"option_id":232,"tag":"K","choice":"b","text":"Saya suka mengatakan kepada orang lain bila mereka mengganggu saya"},"21":{"option_id":233,"tag":"G","choice":"a","text":"Saya selalu mencoba sekuat tenaga"},"22":{"option_id":236,"tag":"C","choice":"b","text":"Saya mengorganisir tugas-tugas secara baik"},"23":{"option_id":238,"tag":"E","choice":"b","text":"Saya seorang yang lambat dalam membuat keputusan"},"24":{"option_id":239,"tag":"X","choice":"a","text":"Saya senang mengerjakan beberapa pekerjaan pada waktu yang bersamaan"},"25":{"option_id":242,"tag":"A","choice":"b","text":"Saya ingin melakukan sesuatu lebih baik dari orang lain"},"26":{"option_id":244,"tag":"P","choice":"b","text":"Saya suka memberi nasihat kepada orang lain"},"27":{"option_id":245,"tag":"Z","choice":"a","text":"Saya suka melakukan hal-hal yang baru dan berbeda"},"28":{"option_id":247,"tag":"K","choice":"a","text":"Bila saya benar, saya suka mempertahankannya \\u201cmati-matian\\u201d"},"29":{"option_id":250,"tag":"O","choice":"b","text":"Saya berusaha untuk sangat intim dengan orang-orang"},"30":{"option_id":252,"tag":"Z","choice":"b","text":"Saya mudah merasa jemu (bosan)"},"31":{"option_id":253,"tag":"G","choice":"a","text":"Saya bekerja \\u201ckeras\\u201d"},"32":{"option_id":256,"tag":"D","choice":"b","text":"Hal-hal yang kecil (detail) menarik hati saya"},"33":{"option_id":258,"tag":"C","choice":"b","text":"Saya meletakkan segala sesuatu secara rapi dan teratur"},"34":{"option_id":259,"tag":"T","choice":"a","text":"Tugas-tugas saya kerjakan secara cepat"},"35":{"option_id":262,"tag":"N","choice":"b","text":"Pada suatu waktu tertentu, saya hanya ingin mengerjakan satu tugas saja"},"36":{"option_id":264,"tag":"A","choice":"b","text":"Saya berusaha keras untuk menjadi yang terbaik"},"37":{"option_id":265,"tag":"Z","choice":"a","text":"Saya menyukai mode baju baru dan tipe-tipe mobil baru"},"38":{"option_id":268,"tag":"X","choice":"b","text":"Saya ingin diperhatikan"},"39":{"option_id":269,"tag":"F","choice":"a","text":"Saya suka menyenangkan hati orang yang memipin saya"},"40":{"option_id":272,"tag":"O","choice":"b","text":"Saya suka orang-orang mengenal saya benar-benar"},"41":{"option_id":273,"tag":"G","choice":"a","text":"Saya mencoba sekuat tenaga"},"42":{"option_id":275,"tag":"L","choice":"a","text":"Orang lain beranggapan bahwa saya adalah seorang pemimpin yang baik"},"43":{"option_id":277,"tag":"I","choice":"a","text":"Seringkali saya memanfaatkan peluang"},"44":{"option_id":280,"tag":"C","choice":"b","text":"Orang lain menganggap saya dapat melakukan penataan yang rapi dan teratur"},"45":{"option_id":282,"tag":"E","choice":"b","text":"Saya sangat menyenangkan"},"46":{"option_id":283,"tag":"O","choice":"a","text":"Saya senang bila orang-orang dapat intim dan bersahabat"},"47":{"option_id":285,"tag":"Z","choice":"a","text":"Saya suka bereksperimen dan mencoba sesuatu yang baru"},"48":{"option_id":287,"tag":"K","choice":"a","text":"Saya senang diperlakukan secara adil"},"49":{"option_id":290,"tag":"X","choice":"b","text":"Saya suka menarik perhatian"},"50":{"option_id":292,"tag":"B","choice":"b","text":"Saya senang berada bersama dengan orang lain"},"51":{"option_id":293,"tag":"G","choice":"a","text":"Saya selalu berusaha mengerjakan tugas secara sempurna"},"52":{"option_id":296,"tag":"S","choice":"b","text":"Saya mudah berteman"},"53":{"option_id":298,"tag":"R","choice":"b","text":"Saya banyak berfikir"},"54":{"option_id":299,"tag":"T","choice":"a","text":"Saya bekerja dengan kecepatan yang mantap dan cepat"},"55":{"option_id":301,"tag":"V","choice":"a","text":"Saya memiliki banyak energi untuk permainan-permainan dan olahraga"},"56":{"option_id":304,"tag":"E","choice":"b","text":"Saya \\u201cpandai mengendalikan diri\\u201d"},"57":{"option_id":306,"tag":"N","choice":"b","text":"Saya selalu ingin menyelesaikan pekerjaan yang sudah saya mulai"},"58":{"option_id":307,"tag":"K","choice":"a","text":"Biasanya saya bersikeras mengenai apa yang saya yakini"},"59":{"option_id":310,"tag":"P","choice":"b","text":"Saya senang mengatur orang lain"},"60":{"option_id":312,"tag":"X","choice":"b","text":"Saya suka menerima banyak perhatian"},"61":{"option_id":313,"tag":"G","choice":"a","text":"Biasanya saya bekerja sangat \\u201ckeras\\u201d"},"62":{"option_id":315,"tag":"L","choice":"a","text":"Bila saya berbicara, kelompok akan mendengarkan"},"63":{"option_id":318,"tag":"S","choice":"b","text":"Saya lambat dalam mengambil keputusan"},"64":{"option_id":319,"tag":"T","choice":"a","text":"Biasanya saya makan secara cepat"},"65":{"option_id":322,"tag":"D","choice":"b","text":"Saya menyukai pekerjaan yang harus dilakukan secara teliti"},"66":{"option_id":324,"tag":"C","choice":"b","text":"Saya dapat menemukan hal-hal yang telah saya pindahkan"},"67":{"option_id":325,"tag":"R","choice":"a","text":"Perencanaan saya jauh ke masa depan"},"68":{"option_id":327,"tag":"K","choice":"a","text":"Saya merasa bangga akan nama baik saya"},"69":{"option_id":329,"tag":"F","choice":"a","text":"Saya suka menyenangkan hati orang-orang yang saya kagumi"},"70":{"option_id":332,"tag":"P","choice":"b","text":"Saya suka mengambil keputusan untuk kelompok"},"71":{"option_id":333,"tag":"G","choice":"a","text":"Saya selalu berusaha sangat \\u201ckeras\\u201d"},"72":{"option_id":336,"tag":"T","choice":"b","text":"Biasanya saya tergesa-gesa"},"73":{"option_id":338,"tag":"V","choice":"b","text":"Saya lambat di dalam mengambil keputusan"},"74":{"option_id":340,"tag":"S","choice":"b","text":"Saya mudah mendapat kawan"},"75":{"option_id":341,"tag":"V","choice":"a","text":"Biasanya saya bersemangat atau bergairah"},"76":{"option_id":343,"tag":"S","choice":"a","text":"Saya sangat hangat kepada orang-orang"},"77":{"option_id":346,"tag":"C","choice":"b","text":"Saya meletakkan segala sesuatu pada tempatnya"},"78":{"option_id":348,"tag":"E","choice":"b","text":"Saya tidak cepat marah"},"79":{"option_id":349,"tag":"F","choice":"a","text":"Saya senang mengikuti orang-orang yang saya kagumi"},"80":{"option_id":351,"tag":"W","choice":"a","text":"Saya menyukai petunjuk-petunjuk yang jelas"},"81":{"option_id":353,"tag":"G","choice":"a","text":"Saya mengejar apa yang saya inginkan"},"82":{"option_id":355,"tag":"L","choice":"a","text":"Saya membuat orang lain bekerja keras"},"83":{"option_id":358,"tag":"T","choice":"b","text":"Bicara saya cepat"},"84":{"option_id":359,"tag":"T","choice":"a","text":"Biasanya saya bekerja tergesa-gesa"},"85":{"option_id":361,"tag":"V","choice":"a","text":"Saya tidak suka bertemu dengan orang-orang"},"86":{"option_id":364,"tag":"R","choice":"b","text":"Banyak waktu saya untuk berpikir"},"87":{"option_id":365,"tag":"R","choice":"a","text":"Saya suka bekerja dengan teori"},"88":{"option_id":368,"tag":"C","choice":"b","text":"Saya suka mengorganisir pekerjaan saya"},"89":{"option_id":370,"tag":"E","choice":"b","text":"Saya selalu menyenangkan"},"90":{"option_id":371,"tag":"W","choice":"a","text":"Saya senang diberi petunjuk mengenai apa yang harus saya lakukan"}}	2026-09-25 08:52:54	2026-09-25 09:52:51
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
samsulhida898@gmail.com	$2y$12$4aMGzXoraq93A7k.b3MtM.EkgBRNOCXojNkbcCLk2NaLKXrBNhH/C	2026-09-02 12:20:36
\.


--
-- Data for Name: positions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.positions (id, department_id, name, description, created_at, updated_at) FROM stdin;
1	4	Frontend Developer	Mengembangkan antarmuka pengguna berbasis web.	2026-09-04 08:15:20	2026-09-04 08:15:20
2	4	Backend Developer	Mengembangkan arsitektur server, API, dan basis data.	2026-09-04 08:15:20	2026-09-04 08:15:20
3	4	IoT Engineer	Merancang dan mengintegrasikan perangkat keras mikrokontroler.	2026-09-04 08:15:20	2026-09-04 08:15:20
4	4	Quality Assurance (QA)	Melakukan pengujian mutu dan otomatisasi sistem perangkat lunak.	2026-09-04 08:15:20	2026-09-04 08:15:20
7	2	Marketing Manager	Memimpin departemen pemasaran, menyusun strategi kampanye secara keseluruhan, dan mengawasi anggaran serta target pasar perusahaan.	2026-09-04 08:29:34	2026-09-04 08:29:34
8	2	Brand Manager	\N	2026-09-04 08:29:57	2026-09-04 08:29:57
5	1	Accounting Manager	Memimpin departemen, memastikan kepatuhan pajak, dan mengawasi laporan keuangan secara keseluruhan.	2026-09-04 08:28:36	2026-09-04 08:32:22
6	1	Finance Staff	Mengelola arus kas harian (cash flow), melakukan pembayaran tagihan, penagihan piutang, dan manajemen perbankan.	2026-09-04 08:28:51	2026-09-04 08:32:42
9	5	Sales Marketing	\N	2026-09-14 14:58:53	2026-09-14 14:58:53
10	2	PS Manufaktur	\N	2026-09-28 11:15:34	2026-09-28 11:15:34
\.


--
-- Data for Name: question_banks; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.question_banks (id, category_id, question, question_type, metadata, image_path, points, created_at, updated_at) FROM stdin;
25	2	Pilihlah satu pernyataan yang Paling Menggambarkan (Most) dan Satu yang Paling Tidak Menggambarkan (Least) diri Anda. (Soal Nomor 1)	disc	{"number":1,"instruction":"Pilih 1 Most (P) dan 1 Least (K)"}	\N	1	2026-08-26 21:28:10	2026-08-26 21:28:10
26	2	Pilihlah satu pernyataan yang Paling Menggambarkan (Most) dan Satu yang Paling Tidak Menggambarkan (Least) diri Anda. (Soal Nomor 2)	disc	{"number":2,"instruction":"Pilih 1 Most (P) dan 1 Least (K)"}	\N	1	2026-08-26 21:28:10	2026-08-26 21:28:10
27	2	Pilihlah satu pernyataan yang Paling Menggambarkan (Most) dan Satu yang Paling Tidak Menggambarkan (Least) diri Anda. (Soal Nomor 3)	disc	{"number":3,"instruction":"Pilih 1 Most (P) dan 1 Least (K)"}	\N	1	2026-08-26 21:28:10	2026-08-26 21:28:10
28	2	Pilihlah satu pernyataan yang Paling Menggambarkan (Most) dan Satu yang Paling Tidak Menggambarkan (Least) diri Anda. (Soal Nomor 4)	disc	{"number":4,"instruction":"Pilih 1 Most (P) dan 1 Least (K)"}	\N	1	2026-08-26 21:28:10	2026-08-26 21:28:10
29	2	Pilihlah satu pernyataan yang Paling Menggambarkan (Most) dan Satu yang Paling Tidak Menggambarkan (Least) diri Anda. (Soal Nomor 5)	disc	{"number":5,"instruction":"Pilih 1 Most (P) dan 1 Least (K)"}	\N	1	2026-08-26 21:28:10	2026-08-26 21:28:10
30	2	Pilihlah satu pernyataan yang Paling Menggambarkan (Most) dan Satu yang Paling Tidak Menggambarkan (Least) diri Anda. (Soal Nomor 6)	disc	{"number":6,"instruction":"Pilih 1 Most (P) dan 1 Least (K)"}	\N	1	2026-08-26 21:28:10	2026-08-26 21:28:10
31	2	Pilihlah satu pernyataan yang Paling Menggambarkan (Most) dan Satu yang Paling Tidak Menggambarkan (Least) diri Anda. (Soal Nomor 7)	disc	{"number":7,"instruction":"Pilih 1 Most (P) dan 1 Least (K)"}	\N	1	2026-08-26 21:28:10	2026-08-26 21:28:10
32	2	Pilihlah satu pernyataan yang Paling Menggambarkan (Most) dan Satu yang Paling Tidak Menggambarkan (Least) diri Anda. (Soal Nomor 8)	disc	{"number":8,"instruction":"Pilih 1 Most (P) dan 1 Least (K)"}	\N	1	2026-08-26 21:28:10	2026-08-26 21:28:10
33	2	Pilihlah satu pernyataan yang Paling Menggambarkan (Most) dan Satu yang Paling Tidak Menggambarkan (Least) diri Anda. (Soal Nomor 9)	disc	{"number":9,"instruction":"Pilih 1 Most (P) dan 1 Least (K)"}	\N	1	2026-08-26 21:28:10	2026-08-26 21:28:10
34	2	Pilihlah satu pernyataan yang Paling Menggambarkan (Most) dan Satu yang Paling Tidak Menggambarkan (Least) diri Anda. (Soal Nomor 10)	disc	{"number":10,"instruction":"Pilih 1 Most (P) dan 1 Least (K)"}	\N	1	2026-08-26 21:28:10	2026-08-26 21:28:10
35	2	Pilihlah satu pernyataan yang Paling Menggambarkan (Most) dan Satu yang Paling Tidak Menggambarkan (Least) diri Anda. (Soal Nomor 11)	disc	{"number":11,"instruction":"Pilih 1 Most (P) dan 1 Least (K)"}	\N	1	2026-08-26 21:28:10	2026-08-26 21:28:10
36	2	Pilihlah satu pernyataan yang Paling Menggambarkan (Most) dan Satu yang Paling Tidak Menggambarkan (Least) diri Anda. (Soal Nomor 12)	disc	{"number":12,"instruction":"Pilih 1 Most (P) dan 1 Least (K)"}	\N	1	2026-08-26 21:28:10	2026-08-26 21:28:10
37	2	Pilihlah satu pernyataan yang Paling Menggambarkan (Most) dan Satu yang Paling Tidak Menggambarkan (Least) diri Anda. (Soal Nomor 13)	disc	{"number":13,"instruction":"Pilih 1 Most (P) dan 1 Least (K)"}	\N	1	2026-08-26 21:28:10	2026-08-26 21:28:10
38	2	Pilihlah satu pernyataan yang Paling Menggambarkan (Most) dan Satu yang Paling Tidak Menggambarkan (Least) diri Anda. (Soal Nomor 14)	disc	{"number":14,"instruction":"Pilih 1 Most (P) dan 1 Least (K)"}	\N	1	2026-08-26 21:28:10	2026-08-26 21:28:10
39	2	Pilihlah satu pernyataan yang Paling Menggambarkan (Most) dan Satu yang Paling Tidak Menggambarkan (Least) diri Anda. (Soal Nomor 15)	disc	{"number":15,"instruction":"Pilih 1 Most (P) dan 1 Least (K)"}	\N	1	2026-08-26 21:28:10	2026-08-26 21:28:10
40	2	Pilihlah satu pernyataan yang Paling Menggambarkan (Most) dan Satu yang Paling Tidak Menggambarkan (Least) diri Anda. (Soal Nomor 16)	disc	{"number":16,"instruction":"Pilih 1 Most (P) dan 1 Least (K)"}	\N	1	2026-08-26 21:28:10	2026-08-26 21:28:10
41	2	Pilihlah satu pernyataan yang Paling Menggambarkan (Most) dan Satu yang Paling Tidak Menggambarkan (Least) diri Anda. (Soal Nomor 17)	disc	{"number":17,"instruction":"Pilih 1 Most (P) dan 1 Least (K)"}	\N	1	2026-08-26 21:28:10	2026-08-26 21:28:10
42	2	Pilihlah satu pernyataan yang Paling Menggambarkan (Most) dan Satu yang Paling Tidak Menggambarkan (Least) diri Anda. (Soal Nomor 18)	disc	{"number":18,"instruction":"Pilih 1 Most (P) dan 1 Least (K)"}	\N	1	2026-08-26 21:28:10	2026-08-26 21:28:10
43	2	Pilihlah satu pernyataan yang Paling Menggambarkan (Most) dan Satu yang Paling Tidak Menggambarkan (Least) diri Anda. (Soal Nomor 19)	disc	{"number":19,"instruction":"Pilih 1 Most (P) dan 1 Least (K)"}	\N	1	2026-08-26 21:28:10	2026-08-26 21:28:10
44	2	Pilihlah satu pernyataan yang Paling Menggambarkan (Most) dan Satu yang Paling Tidak Menggambarkan (Least) diri Anda. (Soal Nomor 20)	disc	{"number":20,"instruction":"Pilih 1 Most (P) dan 1 Least (K)"}	\N	1	2026-08-26 21:28:10	2026-08-26 21:28:10
45	2	Pilihlah satu pernyataan yang Paling Menggambarkan (Most) dan Satu yang Paling Tidak Menggambarkan (Least) diri Anda. (Soal Nomor 21)	disc	{"number":21,"instruction":"Pilih 1 Most (P) dan 1 Least (K)"}	\N	1	2026-08-26 21:28:10	2026-08-26 21:28:10
46	2	Pilihlah satu pernyataan yang Paling Menggambarkan (Most) dan Satu yang Paling Tidak Menggambarkan (Least) diri Anda. (Soal Nomor 22)	disc	{"number":22,"instruction":"Pilih 1 Most (P) dan 1 Least (K)"}	\N	1	2026-08-26 21:28:10	2026-08-26 21:28:10
47	2	Pilihlah satu pernyataan yang Paling Menggambarkan (Most) dan Satu yang Paling Tidak Menggambarkan (Least) diri Anda. (Soal Nomor 23)	disc	{"number":23,"instruction":"Pilih 1 Most (P) dan 1 Least (K)"}	\N	1	2026-08-26 21:28:10	2026-08-26 21:28:10
48	2	Pilihlah satu pernyataan yang Paling Menggambarkan (Most) dan Satu yang Paling Tidak Menggambarkan (Least) diri Anda. (Soal Nomor 24)	disc	{"number":24,"instruction":"Pilih 1 Most (P) dan 1 Least (K)"}	\N	1	2026-08-26 21:28:10	2026-08-26 21:28:10
49	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 1)	papi_kostick	{"number":1,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
50	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 2)	papi_kostick	{"number":2,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
51	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 3)	papi_kostick	{"number":3,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
52	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 4)	papi_kostick	{"number":4,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
53	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 5)	papi_kostick	{"number":5,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
54	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 6)	papi_kostick	{"number":6,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
55	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 7)	papi_kostick	{"number":7,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
56	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 8)	papi_kostick	{"number":8,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
57	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 9)	papi_kostick	{"number":9,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
58	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 10)	papi_kostick	{"number":10,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
59	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 11)	papi_kostick	{"number":11,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
60	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 12)	papi_kostick	{"number":12,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
61	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 13)	papi_kostick	{"number":13,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
62	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 14)	papi_kostick	{"number":14,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
63	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 15)	papi_kostick	{"number":15,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
64	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 16)	papi_kostick	{"number":16,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
65	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 17)	papi_kostick	{"number":17,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
66	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 18)	papi_kostick	{"number":18,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
67	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 19)	papi_kostick	{"number":19,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
68	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 20)	papi_kostick	{"number":20,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
69	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 21)	papi_kostick	{"number":21,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
70	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 22)	papi_kostick	{"number":22,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
71	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 23)	papi_kostick	{"number":23,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
72	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 24)	papi_kostick	{"number":24,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
73	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 25)	papi_kostick	{"number":25,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
74	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 26)	papi_kostick	{"number":26,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
75	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 27)	papi_kostick	{"number":27,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
76	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 28)	papi_kostick	{"number":28,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
77	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 29)	papi_kostick	{"number":29,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
78	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 30)	papi_kostick	{"number":30,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
79	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 31)	papi_kostick	{"number":31,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
80	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 32)	papi_kostick	{"number":32,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
81	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 33)	papi_kostick	{"number":33,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
82	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 34)	papi_kostick	{"number":34,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
83	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 35)	papi_kostick	{"number":35,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
84	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 36)	papi_kostick	{"number":36,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
85	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 37)	papi_kostick	{"number":37,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
86	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 38)	papi_kostick	{"number":38,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
87	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 39)	papi_kostick	{"number":39,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
88	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 40)	papi_kostick	{"number":40,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
89	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 41)	papi_kostick	{"number":41,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
90	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 42)	papi_kostick	{"number":42,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
91	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 43)	papi_kostick	{"number":43,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
92	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 44)	papi_kostick	{"number":44,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
93	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 45)	papi_kostick	{"number":45,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
94	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 46)	papi_kostick	{"number":46,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
95	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 47)	papi_kostick	{"number":47,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
96	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 48)	papi_kostick	{"number":48,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
97	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 49)	papi_kostick	{"number":49,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
98	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 50)	papi_kostick	{"number":50,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
99	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 51)	papi_kostick	{"number":51,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
100	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 52)	papi_kostick	{"number":52,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
101	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 53)	papi_kostick	{"number":53,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
102	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 54)	papi_kostick	{"number":54,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
103	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 55)	papi_kostick	{"number":55,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
104	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 56)	papi_kostick	{"number":56,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
105	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 57)	papi_kostick	{"number":57,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
106	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 58)	papi_kostick	{"number":58,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
107	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 59)	papi_kostick	{"number":59,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
108	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 60)	papi_kostick	{"number":60,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
109	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 61)	papi_kostick	{"number":61,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
110	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 62)	papi_kostick	{"number":62,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
111	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 63)	papi_kostick	{"number":63,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
112	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 64)	papi_kostick	{"number":64,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
113	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 65)	papi_kostick	{"number":65,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:22	2026-09-24 08:19:22
114	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 66)	papi_kostick	{"number":66,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:23	2026-09-24 08:19:23
115	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 67)	papi_kostick	{"number":67,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:23	2026-09-24 08:19:23
116	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 68)	papi_kostick	{"number":68,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:23	2026-09-24 08:19:23
117	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 69)	papi_kostick	{"number":69,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:23	2026-09-24 08:19:23
118	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 70)	papi_kostick	{"number":70,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:23	2026-09-24 08:19:23
119	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 71)	papi_kostick	{"number":71,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:23	2026-09-24 08:19:23
120	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 72)	papi_kostick	{"number":72,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:23	2026-09-24 08:19:23
121	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 73)	papi_kostick	{"number":73,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:23	2026-09-24 08:19:23
122	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 74)	papi_kostick	{"number":74,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:23	2026-09-24 08:19:23
123	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 75)	papi_kostick	{"number":75,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:23	2026-09-24 08:19:23
124	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 76)	papi_kostick	{"number":76,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:23	2026-09-24 08:19:23
125	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 77)	papi_kostick	{"number":77,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:23	2026-09-24 08:19:23
126	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 78)	papi_kostick	{"number":78,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:23	2026-09-24 08:19:23
127	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 79)	papi_kostick	{"number":79,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:23	2026-09-24 08:19:23
128	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 80)	papi_kostick	{"number":80,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:23	2026-09-24 08:19:23
129	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 81)	papi_kostick	{"number":81,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:23	2026-09-24 08:19:23
130	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 82)	papi_kostick	{"number":82,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:23	2026-09-24 08:19:23
131	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 83)	papi_kostick	{"number":83,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:23	2026-09-24 08:19:23
132	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 84)	papi_kostick	{"number":84,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:23	2026-09-24 08:19:23
133	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 85)	papi_kostick	{"number":85,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:23	2026-09-24 08:19:23
134	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 86)	papi_kostick	{"number":86,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:23	2026-09-24 08:19:23
135	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 87)	papi_kostick	{"number":87,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:23	2026-09-24 08:19:23
136	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 88)	papi_kostick	{"number":88,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:23	2026-09-24 08:19:23
137	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 89)	papi_kostick	{"number":89,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:23	2026-09-24 08:19:23
138	3	Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor 90)	papi_kostick	{"number":90,"instruction":"Pilih pernyataan yang paling menggambarkan diri Anda."}	\N	1	2026-09-24 08:19:23	2026-09-24 08:19:23
\.


--
-- Data for Name: question_options; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.question_options (id, question_id, option_text, attribute_tag, is_correct, most_tag, least_tag) FROM stdin;
193	49	Saya seorang pekerja “keras”	G	f	\N	\N
194	49	Saya bukan seorang pemurung	E	f	\N	\N
195	50	Saya suka bekerja lebih baik dari orang lain	A	f	\N	\N
196	50	Saya suka mengerjakan apa yang sedang saya kerjakan, sampai selesai	N	f	\N	\N
197	51	Saya suka menunjukkan caranya melaksanakan sesuatu hal	P	f	\N	\N
198	51	Saya ingin bekerja sebaik mungkin	A	f	\N	\N
199	52	Saya suka berkelakar	X	f	\N	\N
200	52	Saya senang mengatakan kepada orang lain, apa yang harus dilakukannya	P	f	\N	\N
201	53	Saya suka menggabungkan diri dengan kelompok-kelompok	B	f	\N	\N
202	53	Saya suka diperhatikan oleh kelompok-kelompok	X	f	\N	\N
203	54	Saya senang bersahabat intim dengan seseorang	O	f	\N	\N
204	54	Saya senang bersahabat dengan sekolompok orang	B	f	\N	\N
205	55	Saya cepat berubah bila hal itu diperlukan	Z	f	\N	\N
206	55	Saya berusaha untuk intim dengan teman-teman	O	f	\N	\N
207	56	Saya suka “membalas dendam” bila saya benar-benar disakiti	K	f	\N	\N
208	56	Saya suka melakukan hal-hal yang baru dan berbeda	Z	f	\N	\N
209	57	Saya ingin atasan saya menyukai saya	F	f	\N	\N
210	57	Saya suka mengatakan kepada orang lain, bila mereka salah	K	f	\N	\N
211	58	Saya suka mengikuti perintah-perintah yang diberikan kepada saya	W	f	\N	\N
212	58	Saya suka menyenangkan hati orang yang memimpin saya	F	f	\N	\N
213	59	Saya mencoba sekuat tenaga	G	f	\N	\N
214	59	Saya seorang yang tertib. Saya meletakkan segala sesuatu pada tempatnya	C	f	\N	\N
215	60	Saya membuat orang lain melakukan apa yang saya inginkan	L	f	\N	\N
216	60	Saya bukan orang yang cepat gusar	E	f	\N	\N
217	61	Saya suka mengatakan kepada kelompok, apa yang harus dilakukan	P	f	\N	\N
218	61	Saya menekuni satu pekerjaan sampai selesai	N	f	\N	\N
219	62	Saya ingin tampak bersemangat dan menarik	X	f	\N	\N
220	62	Saya ingin menjadi sangat sukses	A	f	\N	\N
221	63	Saya suka menyelaraskan diri dengan kelompok	B	f	\N	\N
222	63	Saya suka membantu orang lain menentukan pendapatnya	P	f	\N	\N
223	64	Saya cemas kalau orang lain tidak menyukai saya	O	f	\N	\N
224	64	Saya senang kalau orang-orang memperhatikan saya	X	f	\N	\N
225	65	Saya suka mencoba sesuatu yang baru	Z	f	\N	\N
226	65	Saya lebih suka bekerja bersama orang-orang daripada bekerja sendiri	B	f	\N	\N
227	66	Kadang-kadang saya menyalahkan orang lain bila terjadi sesuatu kesalahan	K	f	\N	\N
228	66	Saya cemas bila seseorang tidak menyukai saya	O	f	\N	\N
229	67	Saya suka menyenangkan hati orang yang memimpin saya	F	f	\N	\N
230	67	Saya suka mencoba pekerjaan-pekerjaan yang baru dan berbeda	Z	f	\N	\N
231	68	Saya menyukai petunjuk yang terinci untuk melakukan sesuatu pekerjaan	W	f	\N	\N
232	68	Saya suka mengatakan kepada orang lain bila mereka mengganggu saya	K	f	\N	\N
233	69	Saya selalu mencoba sekuat tenaga	G	f	\N	\N
234	69	Saya senang bekerja dengan sangat cermat dan hati-hati	D	f	\N	\N
235	70	Saya adalah seorang pemimpin yang baik	L	f	\N	\N
236	70	Saya mengorganisir tugas-tugas secara baik	C	f	\N	\N
237	71	Saya mudah menjadi gusar	I	f	\N	\N
238	71	Saya seorang yang lambat dalam membuat keputusan	E	f	\N	\N
239	72	Saya senang mengerjakan beberapa pekerjaan pada waktu yang bersamaan	X	f	\N	\N
240	72	Bila dalam kelompok, saya lebih suka diam	N	f	\N	\N
241	73	Saya senang bila diundang	B	f	\N	\N
242	73	Saya ingin melakukan sesuatu lebih baik dari orang lain	A	f	\N	\N
243	74	Saya suka berteman intim dengan teman-teman saya	O	f	\N	\N
244	74	Saya suka memberi nasihat kepada orang lain	P	f	\N	\N
245	75	Saya suka melakukan hal-hal yang baru dan berbeda	Z	f	\N	\N
246	75	Saya suka menceritakan keberhasilan saya dalam mengerjakan tugas	X	f	\N	\N
247	76	Bila saya benar, saya suka mempertahankannya “mati-matian”	K	f	\N	\N
248	76	Saya suka bergabung ke dalam suatu kelompok	B	f	\N	\N
249	77	Saya tidak mau berbeda dengan orang lain	F	f	\N	\N
250	77	Saya berusaha untuk sangat intim dengan orang-orang	O	f	\N	\N
251	78	Saya suka diajari mengenai caranya mengerjakan suatu pekerjaan	W	f	\N	\N
252	78	Saya mudah merasa jemu (bosan)	Z	f	\N	\N
253	79	Saya bekerja “keras”	G	f	\N	\N
254	79	Saya banyak berpikir dan berencana	R	f	\N	\N
255	80	Saya memimpin kelompok	L	f	\N	\N
256	80	Hal-hal yang kecil (detail) menarik hati saya	D	f	\N	\N
257	81	Saya cepat dan mudah mengambil keputusan	I	f	\N	\N
258	81	Saya meletakkan segala sesuatu secara rapi dan teratur	C	f	\N	\N
259	82	Tugas-tugas saya kerjakan secara cepat	T	f	\N	\N
260	82	Saya jarang marah atau sedih	E	f	\N	\N
261	83	Saya ingin menjadi bagian dari kelompok	B	f	\N	\N
262	83	Pada suatu waktu tertentu, saya hanya ingin mengerjakan satu tugas saja	N	f	\N	\N
263	84	Saya berusaha untuk intim dengan teman-teman saya	O	f	\N	\N
264	84	Saya berusaha keras untuk menjadi yang terbaik	A	f	\N	\N
265	85	Saya menyukai mode baju baru dan tipe-tipe mobil baru	Z	f	\N	\N
97	25	Gampang gaul, Mudah setuju	S	f	S	S
98	25	Percaya, Mudah percaya pada orang	I	f	I	I
99	25	Petualang, Mengambil resiko	*	f	*	D
100	25	Toleran, Menghormati	C	f	C	C
101	26	Lembut suara, Pendiam	C	f	C	*
102	26	Optimistik, Visioner	D	f	D	D
103	26	Pusat Perhatian, Suka gaul	*	f	*	I
104	26	Pendamai, Membawa Harmoni	S	f	S	S
105	27	Menyemangati orang	I	f	I	I
106	27	Berusaha sempurna	*	f	*	C
107	27	Bagian dari kelompok	*	f	*	S
108	27	Ingin membuat tujuan	D	f	D	*
109	28	Menjadi frustrasi	C	f	C	C
110	28	Menyimpan perasaan saya	S	f	S	S
111	28	Menceritakan sisi saya	*	f	*	I
112	28	Siap beroposisi	D	f	D	D
113	29	Hidup, Suka bicara	I	f	I	*
114	29	Gerak cepat, Tekun	D	f	D	D
115	29	Usaha menjaga keseimbangan	S	f	S	S
116	29	Usaha mengikuti aturan	*	f	*	C
117	30	Kelola waktu secara efisien	C	f	C	*
118	30	Sering terburu-buru, Merasa tertekan	D	f	D	D
119	30	Masalah sosial itu penting	I	f	I	I
120	30	Suka selesaikan apa yang saya mulai	S	f	S	S
121	31	Tolak perubahan mendadak	S	f	S	*
122	31	Cenderung janji berlebihan	I	f	I	I
123	31	Tarik diri di tengah tekanan	*	f	*	C
124	31	Tidak takut bertempur	*	f	*	D
125	32	Penyemangat yang baik	I	f	I	I
126	32	Pendengar yang baik	S	f	S	S
127	32	Penganalisa yang baik	C	f	C	C
128	32	Delegator yang baik	D	f	D	D
129	33	Hasil adalah penting	D	f	D	D
130	33	Lakukan dengan benar, Akurasi penting	C	f	C	C
131	33	Dibuat menyenangkan	*	f	*	I
132	33	Mari kerjakan bersama	*	f	*	S
133	34	Akan berjalan terus tanpa kontrol diri	*	f	*	C
134	34	Akan membeli sesuai dorongan hati	D	f	D	D
135	34	Akan menunggu, Tanpa tekanan	S	f	S	S
136	34	Akan mengusahakan  yang kuinginkan	I	f	I	*
137	35	Ramah, Mudah bergabung	S	f	S	*
138	35	Unik, Bosan rutinitas	*	f	*	I
139	35	Aktif mengubah sesuatu	D	f	D	D
140	35	Ingin hal-hal yang pasti	C	f	C	C
141	36	Non-konfrontasi, Menyerah	*	f	*	S
142	36	Dipenuhi hal detail	C	f	C	*
143	36	Perubahan pada menit terakhir	I	f	I	I
144	36	Menuntut, Kasar	D	f	D	D
145	37	Ingin kemajuan	D	f	D	D
146	37	Puas dengan segalanya	S	f	S	*
147	37	Terbuka memperlihatkan perasaan	I	f	I	*
148	37	Rendah hati, Sederhana	*	f	*	C
149	38	Tenang, Pendiam	C	f	C	C
150	38	Bahagia, Tanpa beban	I	f	I	I
151	38	Menyenangkan, Baik hati	S	f	S	*
152	38	Tak gentar, Berani	D	f	D	D
153	39	Menggunakan waktu berkualitas dgn teman	S	f	S	S
154	39	Rencanakan masa depan, Bersiap	C	f	C	*
155	39	Bepergian demi petualangan baru	I	f	I	I
156	39	Menerima ganjaran atas tujuan yg dicapai	D	f	D	D
157	40	Aturan perlu dipertanyakan	*	f	*	D
158	40	Aturan membuat adil	C	f	C	*
159	40	Aturan membuat bosan	I	f	I	I
160	40	Aturan membuat aman	S	f	S	S
161	41	Pendidikan, Kebudayaan	*	f	*	C
162	41	Prestasi, Ganjaran	D	f	D	D
163	41	Keselamatan, keamanan	S	f	S	S
164	41	Sosial, Perkumpulan kelompok	I	f	I	*
165	42	Memimpin, Pendekatan langsung	D	f	D	D
166	42	Suka bergaul, Antusias	*	f	*	I
167	42	Dapat diramal, Konsisten	*	f	*	S
168	42	Waspada, Hati-hati	C	f	C	*
169	43	Tidak mudah dikalahkan	D	f	D	D
170	43	Kerjakan sesuai perintah, Ikut pimpinan	S	f	S	*
171	43	Mudah terangsang, Riang	I	f	I	I
172	43	Ingin segalanya teratur, Rapi	*	f	*	C
173	44	Saya akan pimpin mereka	D	f	D	*
174	44	Saya akan melaksanakan	S	f	S	S
175	44	Saya akan meyakinkan mereka	I	f	I	I
176	44	Saya dapatkan fakta	C	f	C	*
177	45	Memikirkan orang dahulu	S	f	S	S
178	45	Kompetitif, Suka tantangan	D	f	D	D
179	45	Optimis, Positif	I	f	I	I
180	45	Pemikir logis, Sistematik	*	f	*	C
181	46	Menyenangkan orang, Mudah setuju	S	f	S	S
182	46	Tertawa lepas, Hidup	*	f	*	I
183	46	Berani, Tak gentar	D	f	D	D
184	46	Tenang, Pendiam	C	f	C	C
185	47	Ingin otoritas lebih	*	f	*	D
186	47	Ingin kesempatan baru	I	f	I	*
187	47	Menghindari konflik	S	f	S	S
188	47	Ingin petunjuk yang jelas	*	f	*	C
189	48	Dapat diandalkan, Dapata dipercaya	*	f	*	S
190	48	Kreatif, Unik	I	f	I	I
191	48	Garis dasar, Orientasi hasil	D	f	D	*
192	48	Jalankan standar yang tinggi, Akurat	C	f	C	*
266	85	Saya ingin menjadi penanggung jawab bagi orang-orang lain	P	f	\N	\N
267	86	Saya suka berdebat	K	f	\N	\N
268	86	Saya ingin diperhatikan	X	f	\N	\N
269	87	Saya suka menyenangkan hati orang yang memipin saya	F	f	\N	\N
270	87	Saya tertarik menjadi anggota dari suatu kelompok	B	f	\N	\N
271	88	Saya senang mengikuti aturan secara tertib	W	f	\N	\N
272	88	Saya suka orang-orang mengenal saya benar-benar	O	f	\N	\N
273	89	Saya mencoba sekuat tenaga	G	f	\N	\N
274	89	Saya sangat menyenangkan	S	f	\N	\N
275	90	Orang lain beranggapan bahwa saya adalah seorang pemimpin yang baik	L	f	\N	\N
276	90	Saya berpikir jauh ke depan dan terinci	R	f	\N	\N
277	91	Seringkali saya memanfaatkan peluang	I	f	\N	\N
278	91	Saya senang memperhatikan hal-hal sampai sekecil-kecilnya	D	f	\N	\N
279	92	Orang lain menganggap saya bekerja cepat	T	f	\N	\N
280	92	Orang lain menganggap saya dapat melakukan penataan yang rapi dan teratur	C	f	\N	\N
281	93	Saya menyukai permainan-permainan dan olahraga	V	f	\N	\N
282	93	Saya sangat menyenangkan	E	f	\N	\N
283	94	Saya senang bila orang-orang dapat intim dan bersahabat	O	f	\N	\N
284	94	Saya selalu berusaha menyelesaikan apa yang telah saya mulai	N	f	\N	\N
285	95	Saya suka bereksperimen dan mencoba sesuatu yang baru	Z	f	\N	\N
286	95	Saya suka mengerjakan pekerjaan-pekerjaan yang sulit dengan baik	A	f	\N	\N
287	96	Saya senang diperlakukan secara adil	K	f	\N	\N
288	96	Saya senang mengajari orang lain bagaimana caranya mengerjakan sesuatu	P	f	\N	\N
289	97	Saya suka mengerjakan apa yang diharapkan dari saya	F	f	\N	\N
290	97	Saya suka menarik perhatian	X	f	\N	\N
291	98	Saya suka petunjuk-petunjuk terinci dalam melaksanakan pekerjaan	W	f	\N	\N
292	98	Saya senang berada bersama dengan orang lain	B	f	\N	\N
293	99	Saya selalu berusaha mengerjakan tugas secara sempurna	G	f	\N	\N
294	99	Orang lain menganggap, saya tidak mengenal lelah, dalam kerja sehari-hari	V	f	\N	\N
295	100	Saya tergolong tipe pemimpin	L	f	\N	\N
296	100	Saya mudah berteman	S	f	\N	\N
297	101	Saya memanfaatkan peluang-peluang	I	f	\N	\N
298	101	Saya banyak berfikir	R	f	\N	\N
299	102	Saya bekerja dengan kecepatan yang mantap dan cepat	T	f	\N	\N
300	102	Saya senang mengerjakan hal-hal yang detail	D	f	\N	\N
301	103	Saya memiliki banyak energi untuk permainan-permainan dan olahraga	V	f	\N	\N
302	103	Saya menempatkan segala sesuatunya secara rapi dan teratur	C	f	\N	\N
303	104	Saya bergaul baik dengan semua orang	S	f	\N	\N
304	104	Saya “pandai mengendalikan diri”	E	f	\N	\N
305	105	Saya ingin berkenalan dengan orang-orang baru dan mengerjakan hal baru	Z	f	\N	\N
306	105	Saya selalu ingin menyelesaikan pekerjaan yang sudah saya mulai	N	f	\N	\N
307	106	Biasanya saya bersikeras mengenai apa yang saya yakini	K	f	\N	\N
308	106	Biasanya saya suka bekerja “keras”	A	f	\N	\N
309	107	Saya menyukai saran-saran dari orang-orang yang saya kagumi	F	f	\N	\N
310	107	Saya senang mengatur orang lain	P	f	\N	\N
311	108	Saya biarkan orang-orang lain mempengaruhi saya	W	f	\N	\N
312	108	Saya suka menerima banyak perhatian	X	f	\N	\N
313	109	Biasanya saya bekerja sangat “keras”	G	f	\N	\N
314	109	Biasanya saya bekerja cepat	T	f	\N	\N
315	110	Bila saya berbicara, kelompok akan mendengarkan	L	f	\N	\N
316	110	Saya terampil mempergunakan alat-alat kerja	V	f	\N	\N
317	111	Saya lambat membina persahabatan	I	f	\N	\N
318	111	Saya lambat dalam mengambil keputusan	S	f	\N	\N
319	112	Biasanya saya makan secara cepat	T	f	\N	\N
320	112	Saya suka membaca	R	f	\N	\N
321	113	Saya menyukai pekerjaan yang memungkinkan saya “berkeliling”	V	f	\N	\N
322	113	Saya menyukai pekerjaan yang harus dilakukan secara teliti	D	f	\N	\N
323	114	Saya berteman sebanyak mungkin	S	f	\N	\N
324	114	Saya dapat menemukan hal-hal yang telah saya pindahkan	C	f	\N	\N
325	115	Perencanaan saya jauh ke masa depan	R	f	\N	\N
326	115	Saya selalu menyenangkan	E	f	\N	\N
327	116	Saya merasa bangga akan nama baik saya	K	f	\N	\N
328	116	Saya tetap menekuni satu permasalahan sampai ia terselesaikan	N	f	\N	\N
329	117	Saya suka menyenangkan hati orang-orang yang saya kagumi	F	f	\N	\N
330	117	Saya suka menjadi seorang yang berhasil	A	f	\N	\N
331	118	Saya senang bila orang-orang lain mengambil keputusan untuk kelompok	W	f	\N	\N
332	118	Saya suka mengambil keputusan untuk kelompok	P	f	\N	\N
333	119	Saya selalu berusaha sangat “keras”	G	f	\N	\N
334	119	Saya cepat dan mudah mengambil keputusan	I	f	\N	\N
335	120	Biasanya kelompok saya mengerjakan hal-hal yang saya inginkan	L	f	\N	\N
336	120	Biasanya saya tergesa-gesa	T	f	\N	\N
337	121	Saya seringkali merasa lelah	I	f	\N	\N
338	121	Saya lambat di dalam mengambil keputusan	V	f	\N	\N
339	122	Saya bekerja secara cepat	T	f	\N	\N
340	122	Saya mudah mendapat kawan	S	f	\N	\N
341	123	Biasanya saya bersemangat atau bergairah	V	f	\N	\N
342	123	Sebagian besar waktu saya untuk berpikir	R	f	\N	\N
343	124	Saya sangat hangat kepada orang-orang	S	f	\N	\N
344	124	Saya menyukai pekerjaan yang menuntut ketepatan	D	f	\N	\N
345	125	Saya banyak berpikir dan merencana	R	f	\N	\N
346	125	Saya meletakkan segala sesuatu pada tempatnya	C	f	\N	\N
347	126	Saya suka tugas yang perlu ditekuni sampai kepada hal sedetilnya	D	f	\N	\N
348	126	Saya tidak cepat marah	E	f	\N	\N
349	127	Saya senang mengikuti orang-orang yang saya kagumi	F	f	\N	\N
350	127	Saya selalu menyelesaikan pekerjaan yang saya mulai	N	f	\N	\N
351	128	Saya menyukai petunjuk-petunjuk yang jelas	W	f	\N	\N
352	128	Saya suka bekerja “keras”	A	f	\N	\N
353	129	Saya mengejar apa yang saya inginkan	G	f	\N	\N
354	129	Saya adalah seorang pemimpin yang baik	L	f	\N	\N
355	130	Saya membuat orang lain bekerja keras	L	f	\N	\N
356	130	Saya adalah seorang yang “gampangan” (tak banyak pertimbangan)	I	f	\N	\N
357	131	Saya membuat keputusan-keputusan secara cepat	I	f	\N	\N
358	131	Bicara saya cepat	T	f	\N	\N
359	132	Biasanya saya bekerja tergesa-gesa	T	f	\N	\N
360	132	Secara teratur saya berolahraga	V	f	\N	\N
361	133	Saya tidak suka bertemu dengan orang-orang	V	f	\N	\N
362	133	Saya cepat lelah	S	f	\N	\N
363	134	Saya mempunyai banyak sekali teman	S	f	\N	\N
364	134	Banyak waktu saya untuk berpikir	R	f	\N	\N
365	135	Saya suka bekerja dengan teori	R	f	\N	\N
366	135	Saya suka bekerja sedetil-detilnya	D	f	\N	\N
367	136	Saya suka bekerja sampai sedetil-detilnya	D	f	\N	\N
368	136	Saya suka mengorganisir pekerjaan saya	C	f	\N	\N
369	137	Saya meletakkan segala sesuatu pada tempatnya	C	f	\N	\N
370	137	Saya selalu menyenangkan	E	f	\N	\N
371	138	Saya senang diberi petunjuk mengenai apa yang harus saya lakukan	W	f	\N	\N
372	138	Saya harus menyelesaikan apa yang sudah saya mulai	N	f	\N	\N
\.


--
-- Data for Name: queue_jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.queue_jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.roles (id, name) FROM stdin;
1	Admin
2	Recruiter
3	Applicant
4	Employee
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
n6EWHPwEew6VzRO4UmCUrLHOawUOZaKVzn4zZ6Rf	\N	2404:8000:1039:539:7d32:accc:43cf:3092	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36	eyJfdG9rZW4iOiJGRGJPaDNwaGJSV1hvckw2dVE2bDh6MDhSNWIxOGRFTXRYSWRSOFdVIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9oZWF0LWdlcm1pY2lkZS1jbGFzcC5uZ3Jvay1mcmVlLmRldlwvbG93b25nYW5cLzQiLCJyb3V0ZSI6ImpvYnMuc2hvdyJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790656414
XPwj47AgcwxlJRA7BNuETBcQbEZoe8z2w3OkX9dj	\N	2404:8000:1039:539:c84:9b34:c5c5:b51d	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36	eyJfdG9rZW4iOiJYTHNuMEpLR1NwT0c3WUlrcUZkZExTZUpGOWw4MkU3VzFrUnB6QzNpIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cHM6XC9cL2hlYXQtZ2VybWljaWRlLWNsYXNwLm5ncm9rLWZyZWUuZGV2XC9sb2dpbj90aW1lb3V0PTEiLCJyb3V0ZSI6ImxvZ2luIn19	1790662429
wCQPijZFspzxCdKR5tm7s6uekxDVkecfSv3OjEUW	1	2404:8000:1039:539:c1b2:26db:a57f:180a	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36	eyJfdG9rZW4iOiI5VWg1WlNmV3l0enpoenlQSDBuMHRFUkljS3lwVE56dWZ5NkV6ZGp6IiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cHM6XC9cL2hlYXQtZ2VybWljaWRlLWNsYXNwLm5ncm9rLWZyZWUuZGV2XC9sb3dvbmdhbiIsInJvdXRlIjoiam9icy5pbmRleCJ9LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MSwic2Vzc2lvbl9zdGFydF90aW1lIjoxNzkwNjY1NjA1LCJsYXN0X2FjdGl2aXR5X3RpbWUiOjE3OTA2NzQwNDR9	1790674044
\.


--
-- Data for Name: skills; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.skills (id, profile_id, name, certificate_path) FROM stdin;
1	1	PHP LARAVEL	\N
2	2	PHP	\N
3	3	PHP Laravel	\N
\.


--
-- Data for Name: social_medias; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.social_medias (id, profile_id, platform_name, url) FROM stdin;
\.


--
-- Data for Name: test_answers; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.test_answers (id, attempt_id, question_id, option_id, answer_type, essay_answer, score, reviewed_by, attachment_url, attachment_name, attachment_size) FROM stdin;
49	2	34	133	most	\N	\N	\N	\N	\N	\N
50	2	34	135	least	\N	\N	\N	\N	\N	\N
51	2	48	189	least	\N	\N	\N	\N	\N	\N
52	2	48	191	most	\N	\N	\N	\N	\N	\N
53	2	33	130	least	\N	\N	\N	\N	\N	\N
54	2	33	131	most	\N	\N	\N	\N	\N	\N
55	2	41	161	least	\N	\N	\N	\N	\N	\N
56	2	41	163	most	\N	\N	\N	\N	\N	\N
57	2	29	113	most	\N	\N	\N	\N	\N	\N
58	2	29	115	least	\N	\N	\N	\N	\N	\N
59	2	36	141	most	\N	\N	\N	\N	\N	\N
60	2	36	143	least	\N	\N	\N	\N	\N	\N
61	2	44	173	least	\N	\N	\N	\N	\N	\N
62	2	44	175	most	\N	\N	\N	\N	\N	\N
63	2	28	110	most	\N	\N	\N	\N	\N	\N
64	2	28	112	least	\N	\N	\N	\N	\N	\N
65	2	37	145	most	\N	\N	\N	\N	\N	\N
66	2	37	147	least	\N	\N	\N	\N	\N	\N
67	2	43	169	least	\N	\N	\N	\N	\N	\N
68	2	43	171	most	\N	\N	\N	\N	\N	\N
69	2	46	183	least	\N	\N	\N	\N	\N	\N
70	2	35	138	most	\N	\N	\N	\N	\N	\N
71	2	35	140	least	\N	\N	\N	\N	\N	\N
72	2	30	119	least	\N	\N	\N	\N	\N	\N
73	2	30	117	most	\N	\N	\N	\N	\N	\N
74	2	39	156	most	\N	\N	\N	\N	\N	\N
75	2	39	154	least	\N	\N	\N	\N	\N	\N
76	2	40	159	most	\N	\N	\N	\N	\N	\N
77	2	40	160	least	\N	\N	\N	\N	\N	\N
78	2	47	185	most	\N	\N	\N	\N	\N	\N
79	2	47	186	least	\N	\N	\N	\N	\N	\N
80	2	25	100	least	\N	\N	\N	\N	\N	\N
81	2	25	98	most	\N	\N	\N	\N	\N	\N
82	2	45	177	least	\N	\N	\N	\N	\N	\N
83	2	45	179	most	\N	\N	\N	\N	\N	\N
84	2	26	102	most	\N	\N	\N	\N	\N	\N
85	2	26	104	least	\N	\N	\N	\N	\N	\N
86	2	42	166	least	\N	\N	\N	\N	\N	\N
87	2	42	168	most	\N	\N	\N	\N	\N	\N
88	2	27	107	most	\N	\N	\N	\N	\N	\N
89	2	27	108	least	\N	\N	\N	\N	\N	\N
90	2	38	152	most	\N	\N	\N	\N	\N	\N
91	2	38	151	least	\N	\N	\N	\N	\N	\N
92	2	31	122	most	\N	\N	\N	\N	\N	\N
93	2	31	123	least	\N	\N	\N	\N	\N	\N
94	2	32	126	most	\N	\N	\N	\N	\N	\N
95	2	32	125	least	\N	\N	\N	\N	\N	\N
96	2	46	181	most	\N	\N	\N	\N	\N	\N
97	3	31	122	least	\N	\N	\N	\N	\N	\N
98	3	31	124	most	\N	\N	\N	\N	\N	\N
99	3	26	102	most	\N	\N	\N	\N	\N	\N
100	3	26	103	least	\N	\N	\N	\N	\N	\N
101	3	46	181	least	\N	\N	\N	\N	\N	\N
102	3	46	183	most	\N	\N	\N	\N	\N	\N
103	3	36	142	most	\N	\N	\N	\N	\N	\N
104	3	36	144	least	\N	\N	\N	\N	\N	\N
105	3	47	185	least	\N	\N	\N	\N	\N	\N
106	3	47	186	most	\N	\N	\N	\N	\N	\N
107	3	44	175	most	\N	\N	\N	\N	\N	\N
108	3	44	176	least	\N	\N	\N	\N	\N	\N
109	3	45	177	least	\N	\N	\N	\N	\N	\N
110	3	45	179	most	\N	\N	\N	\N	\N	\N
111	3	27	105	least	\N	\N	\N	\N	\N	\N
112	3	27	108	most	\N	\N	\N	\N	\N	\N
113	3	35	137	most	\N	\N	\N	\N	\N	\N
114	3	35	138	least	\N	\N	\N	\N	\N	\N
115	3	25	97	least	\N	\N	\N	\N	\N	\N
116	3	25	99	most	\N	\N	\N	\N	\N	\N
117	3	38	152	most	\N	\N	\N	\N	\N	\N
118	3	38	151	least	\N	\N	\N	\N	\N	\N
119	3	42	166	most	\N	\N	\N	\N	\N	\N
120	3	42	167	least	\N	\N	\N	\N	\N	\N
121	3	29	114	most	\N	\N	\N	\N	\N	\N
122	3	29	113	least	\N	\N	\N	\N	\N	\N
123	3	40	160	most	\N	\N	\N	\N	\N	\N
124	3	40	157	least	\N	\N	\N	\N	\N	\N
125	3	48	189	most	\N	\N	\N	\N	\N	\N
126	3	48	190	least	\N	\N	\N	\N	\N	\N
127	3	41	161	most	\N	\N	\N	\N	\N	\N
128	3	41	162	least	\N	\N	\N	\N	\N	\N
129	3	37	145	most	\N	\N	\N	\N	\N	\N
130	3	37	147	least	\N	\N	\N	\N	\N	\N
131	3	39	155	most	\N	\N	\N	\N	\N	\N
132	3	39	156	least	\N	\N	\N	\N	\N	\N
133	3	43	172	most	\N	\N	\N	\N	\N	\N
134	3	43	171	least	\N	\N	\N	\N	\N	\N
135	3	33	130	most	\N	\N	\N	\N	\N	\N
136	3	33	131	least	\N	\N	\N	\N	\N	\N
137	3	30	117	most	\N	\N	\N	\N	\N	\N
138	3	30	118	least	\N	\N	\N	\N	\N	\N
139	3	32	126	most	\N	\N	\N	\N	\N	\N
140	3	32	125	least	\N	\N	\N	\N	\N	\N
141	3	28	110	most	\N	\N	\N	\N	\N	\N
142	3	28	111	least	\N	\N	\N	\N	\N	\N
143	3	34	136	most	\N	\N	\N	\N	\N	\N
144	3	34	135	least	\N	\N	\N	\N	\N	\N
145	4	25	99	most	\N	\N	\N	\N	\N	\N
147	4	26	102	most	\N	\N	\N	\N	\N	\N
146	4	25	97	least	\N	\N	\N	\N	\N	\N
148	4	26	101	least	\N	\N	\N	\N	\N	\N
149	4	27	108	most	\N	\N	\N	\N	\N	\N
150	4	27	105	least	\N	\N	\N	\N	\N	\N
151	4	28	111	most	\N	\N	\N	\N	\N	\N
152	4	28	109	least	\N	\N	\N	\N	\N	\N
153	4	29	114	most	\N	\N	\N	\N	\N	\N
154	4	29	115	least	\N	\N	\N	\N	\N	\N
155	4	30	120	most	\N	\N	\N	\N	\N	\N
156	4	30	118	least	\N	\N	\N	\N	\N	\N
157	4	31	124	most	\N	\N	\N	\N	\N	\N
158	4	31	122	least	\N	\N	\N	\N	\N	\N
159	4	32	126	most	\N	\N	\N	\N	\N	\N
160	4	32	125	least	\N	\N	\N	\N	\N	\N
161	4	33	130	most	\N	\N	\N	\N	\N	\N
162	4	33	131	least	\N	\N	\N	\N	\N	\N
163	4	34	136	most	\N	\N	\N	\N	\N	\N
164	4	34	133	least	\N	\N	\N	\N	\N	\N
165	4	35	137	most	\N	\N	\N	\N	\N	\N
166	4	35	138	least	\N	\N	\N	\N	\N	\N
167	4	36	142	most	\N	\N	\N	\N	\N	\N
168	4	36	144	least	\N	\N	\N	\N	\N	\N
169	4	37	145	most	\N	\N	\N	\N	\N	\N
170	4	37	146	least	\N	\N	\N	\N	\N	\N
171	4	38	151	most	\N	\N	\N	\N	\N	\N
172	4	38	149	least	\N	\N	\N	\N	\N	\N
173	4	39	154	most	\N	\N	\N	\N	\N	\N
174	4	39	153	least	\N	\N	\N	\N	\N	\N
175	4	40	160	most	\N	\N	\N	\N	\N	\N
176	4	40	159	least	\N	\N	\N	\N	\N	\N
177	4	41	164	most	\N	\N	\N	\N	\N	\N
178	4	41	161	least	\N	\N	\N	\N	\N	\N
179	4	42	166	most	\N	\N	\N	\N	\N	\N
180	4	42	167	least	\N	\N	\N	\N	\N	\N
181	4	43	172	most	\N	\N	\N	\N	\N	\N
182	4	43	171	least	\N	\N	\N	\N	\N	\N
183	4	44	174	most	\N	\N	\N	\N	\N	\N
184	4	44	173	least	\N	\N	\N	\N	\N	\N
185	4	45	179	most	\N	\N	\N	\N	\N	\N
186	4	45	177	least	\N	\N	\N	\N	\N	\N
187	4	46	182	most	\N	\N	\N	\N	\N	\N
188	4	46	184	least	\N	\N	\N	\N	\N	\N
189	4	47	188	most	\N	\N	\N	\N	\N	\N
190	4	47	185	least	\N	\N	\N	\N	\N	\N
191	4	48	189	most	\N	\N	\N	\N	\N	\N
192	4	48	190	least	\N	\N	\N	\N	\N	\N
193	5	25	97	least	\N	\N	\N	\N	\N	\N
194	5	25	98	most	\N	\N	\N	\N	\N	\N
195	5	26	101	most	\N	\N	\N	\N	\N	\N
196	5	26	102	least	\N	\N	\N	\N	\N	\N
197	5	27	105	most	\N	\N	\N	\N	\N	\N
198	5	27	106	least	\N	\N	\N	\N	\N	\N
199	5	28	109	most	\N	\N	\N	\N	\N	\N
200	5	28	110	least	\N	\N	\N	\N	\N	\N
201	5	29	113	most	\N	\N	\N	\N	\N	\N
202	5	29	114	least	\N	\N	\N	\N	\N	\N
203	5	30	117	most	\N	\N	\N	\N	\N	\N
204	5	30	118	least	\N	\N	\N	\N	\N	\N
205	5	31	121	most	\N	\N	\N	\N	\N	\N
206	5	31	122	least	\N	\N	\N	\N	\N	\N
207	5	32	125	most	\N	\N	\N	\N	\N	\N
208	5	32	126	least	\N	\N	\N	\N	\N	\N
209	5	33	129	most	\N	\N	\N	\N	\N	\N
210	5	33	130	least	\N	\N	\N	\N	\N	\N
211	5	34	135	least	\N	\N	\N	\N	\N	\N
212	5	34	134	most	\N	\N	\N	\N	\N	\N
213	5	35	140	most	\N	\N	\N	\N	\N	\N
214	5	35	138	least	\N	\N	\N	\N	\N	\N
215	5	36	141	most	\N	\N	\N	\N	\N	\N
216	5	36	144	least	\N	\N	\N	\N	\N	\N
217	5	37	145	least	\N	\N	\N	\N	\N	\N
218	5	37	146	most	\N	\N	\N	\N	\N	\N
219	5	38	149	most	\N	\N	\N	\N	\N	\N
220	5	38	152	least	\N	\N	\N	\N	\N	\N
221	5	39	153	least	\N	\N	\N	\N	\N	\N
222	5	39	156	most	\N	\N	\N	\N	\N	\N
223	5	40	157	least	\N	\N	\N	\N	\N	\N
224	5	40	160	most	\N	\N	\N	\N	\N	\N
225	5	41	161	least	\N	\N	\N	\N	\N	\N
226	5	41	162	most	\N	\N	\N	\N	\N	\N
227	5	42	165	most	\N	\N	\N	\N	\N	\N
228	5	42	167	least	\N	\N	\N	\N	\N	\N
229	5	43	169	most	\N	\N	\N	\N	\N	\N
230	5	43	171	least	\N	\N	\N	\N	\N	\N
231	5	44	173	most	\N	\N	\N	\N	\N	\N
232	5	44	176	least	\N	\N	\N	\N	\N	\N
233	5	45	177	most	\N	\N	\N	\N	\N	\N
234	5	45	179	least	\N	\N	\N	\N	\N	\N
235	5	46	181	least	\N	\N	\N	\N	\N	\N
236	5	46	183	most	\N	\N	\N	\N	\N	\N
237	5	47	187	least	\N	\N	\N	\N	\N	\N
238	5	47	186	most	\N	\N	\N	\N	\N	\N
239	5	48	189	most	\N	\N	\N	\N	\N	\N
240	5	48	190	least	\N	\N	\N	\N	\N	\N
247	6	47	187	least	\N	\N	\N	\N	\N	\N
249	6	44	175	most	\N	\N	\N	\N	\N	\N
250	6	44	176	least	\N	\N	\N	\N	\N	\N
242	6	48	189	least	\N	\N	\N	\N	\N	\N
241	6	48	190	most	\N	\N	\N	\N	\N	\N
243	6	46	181	most	\N	\N	\N	\N	\N	\N
244	6	46	184	least	\N	\N	\N	\N	\N	\N
246	6	47	186	most	\N	\N	\N	\N	\N	\N
248	6	45	179	most	\N	\N	\N	\N	\N	\N
245	6	45	177	least	\N	\N	\N	\N	\N	\N
251	6	43	171	most	\N	\N	\N	\N	\N	\N
252	6	43	170	least	\N	\N	\N	\N	\N	\N
253	6	42	166	most	\N	\N	\N	\N	\N	\N
254	6	42	167	least	\N	\N	\N	\N	\N	\N
255	6	41	162	most	\N	\N	\N	\N	\N	\N
256	6	41	164	least	\N	\N	\N	\N	\N	\N
258	6	40	159	least	\N	\N	\N	\N	\N	\N
257	6	40	157	most	\N	\N	\N	\N	\N	\N
259	6	39	153	least	\N	\N	\N	\N	\N	\N
260	6	39	154	most	\N	\N	\N	\N	\N	\N
262	6	38	149	least	\N	\N	\N	\N	\N	\N
263	6	38	151	most	\N	\N	\N	\N	\N	\N
385	9	48	189	least	\N	\N	\N	\N	\N	\N
265	6	37	147	least	\N	\N	\N	\N	\N	\N
266	6	37	145	most	\N	\N	\N	\N	\N	\N
267	6	36	144	least	\N	\N	\N	\N	\N	\N
386	9	48	190	most	\N	\N	\N	\N	\N	\N
387	9	47	185	most	\N	\N	\N	\N	\N	\N
270	6	36	142	most	\N	\N	\N	\N	\N	\N
272	6	35	140	least	\N	\N	\N	\N	\N	\N
388	9	47	186	least	\N	\N	\N	\N	\N	\N
271	6	35	138	most	\N	\N	\N	\N	\N	\N
389	9	46	181	most	\N	\N	\N	\N	\N	\N
275	6	34	135	least	\N	\N	\N	\N	\N	\N
276	6	34	136	most	\N	\N	\N	\N	\N	\N
390	9	46	182	least	\N	\N	\N	\N	\N	\N
278	6	33	131	most	\N	\N	\N	\N	\N	\N
391	9	45	178	most	\N	\N	\N	\N	\N	\N
280	6	33	129	least	\N	\N	\N	\N	\N	\N
392	9	45	177	least	\N	\N	\N	\N	\N	\N
393	9	44	173	most	\N	\N	\N	\N	\N	\N
281	6	32	125	most	\N	\N	\N	\N	\N	\N
394	9	44	174	least	\N	\N	\N	\N	\N	\N
283	6	32	127	least	\N	\N	\N	\N	\N	\N
395	9	43	169	most	\N	\N	\N	\N	\N	\N
396	9	43	170	least	\N	\N	\N	\N	\N	\N
284	6	31	121	least	\N	\N	\N	\N	\N	\N
397	9	42	165	most	\N	\N	\N	\N	\N	\N
398	9	42	166	least	\N	\N	\N	\N	\N	\N
287	6	31	124	most	\N	\N	\N	\N	\N	\N
288	6	30	117	most	\N	\N	\N	\N	\N	\N
289	6	30	118	least	\N	\N	\N	\N	\N	\N
399	9	41	161	least	\N	\N	\N	\N	\N	\N
291	6	29	116	least	\N	\N	\N	\N	\N	\N
292	6	29	114	most	\N	\N	\N	\N	\N	\N
400	9	41	162	most	\N	\N	\N	\N	\N	\N
401	9	40	157	least	\N	\N	\N	\N	\N	\N
402	9	40	158	most	\N	\N	\N	\N	\N	\N
293	6	28	112	most	\N	\N	\N	\N	\N	\N
295	6	28	109	least	\N	\N	\N	\N	\N	\N
403	9	39	154	most	\N	\N	\N	\N	\N	\N
404	9	39	153	least	\N	\N	\N	\N	\N	\N
298	6	27	105	most	\N	\N	\N	\N	\N	\N
405	9	38	150	most	\N	\N	\N	\N	\N	\N
299	6	27	106	least	\N	\N	\N	\N	\N	\N
406	9	38	149	least	\N	\N	\N	\N	\N	\N
407	9	37	145	most	\N	\N	\N	\N	\N	\N
408	9	37	146	least	\N	\N	\N	\N	\N	\N
409	9	36	141	most	\N	\N	\N	\N	\N	\N
300	6	26	101	least	\N	\N	\N	\N	\N	\N
410	9	36	142	least	\N	\N	\N	\N	\N	\N
301	6	26	104	most	\N	\N	\N	\N	\N	\N
411	9	35	137	most	\N	\N	\N	\N	\N	\N
412	9	35	138	least	\N	\N	\N	\N	\N	\N
304	6	25	100	most	\N	\N	\N	\N	\N	\N
305	6	25	97	least	\N	\N	\N	\N	\N	\N
413	9	34	133	most	\N	\N	\N	\N	\N	\N
414	9	34	134	least	\N	\N	\N	\N	\N	\N
415	9	33	129	most	\N	\N	\N	\N	\N	\N
416	9	33	130	least	\N	\N	\N	\N	\N	\N
417	9	32	125	most	\N	\N	\N	\N	\N	\N
418	9	32	126	least	\N	\N	\N	\N	\N	\N
419	9	31	121	most	\N	\N	\N	\N	\N	\N
420	9	31	122	least	\N	\N	\N	\N	\N	\N
421	9	30	119	most	\N	\N	\N	\N	\N	\N
422	9	30	118	least	\N	\N	\N	\N	\N	\N
423	9	29	115	most	\N	\N	\N	\N	\N	\N
424	9	29	114	least	\N	\N	\N	\N	\N	\N
425	9	28	109	most	\N	\N	\N	\N	\N	\N
426	9	28	110	least	\N	\N	\N	\N	\N	\N
427	9	27	107	most	\N	\N	\N	\N	\N	\N
428	9	27	106	least	\N	\N	\N	\N	\N	\N
429	9	26	101	most	\N	\N	\N	\N	\N	\N
430	9	26	102	least	\N	\N	\N	\N	\N	\N
431	9	25	99	most	\N	\N	\N	\N	\N	\N
432	9	25	98	least	\N	\N	\N	\N	\N	\N
433	10	48	189	most	\N	\N	\N	\N	\N	\N
434	10	48	192	least	\N	\N	\N	\N	\N	\N
435	10	47	185	least	\N	\N	\N	\N	\N	\N
436	10	47	186	most	\N	\N	\N	\N	\N	\N
437	10	46	182	most	\N	\N	\N	\N	\N	\N
438	10	46	181	least	\N	\N	\N	\N	\N	\N
440	10	45	180	most	\N	\N	\N	\N	\N	\N
439	10	45	178	least	\N	\N	\N	\N	\N	\N
441	11	48	189	most	\N	\N	\N	\N	\N	\N
443	10	44	173	least	\N	\N	\N	\N	\N	\N
442	11	48	191	least	\N	\N	\N	\N	\N	\N
444	11	47	185	least	\N	\N	\N	\N	\N	\N
446	11	47	188	most	\N	\N	\N	\N	\N	\N
447	11	46	181	least	\N	\N	\N	\N	\N	\N
452	10	43	172	most	\N	\N	\N	\N	\N	\N
448	11	46	183	most	\N	\N	\N	\N	\N	\N
453	10	42	167	least	\N	\N	\N	\N	\N	\N
449	11	45	177	least	\N	\N	\N	\N	\N	\N
445	10	44	176	most	\N	\N	\N	\N	\N	\N
451	10	43	169	least	\N	\N	\N	\N	\N	\N
450	11	45	180	most	\N	\N	\N	\N	\N	\N
456	11	44	173	least	\N	\N	\N	\N	\N	\N
454	10	42	166	most	\N	\N	\N	\N	\N	\N
455	11	44	175	most	\N	\N	\N	\N	\N	\N
457	10	41	162	least	\N	\N	\N	\N	\N	\N
458	10	41	161	most	\N	\N	\N	\N	\N	\N
459	11	43	172	most	\N	\N	\N	\N	\N	\N
460	10	40	158	most	\N	\N	\N	\N	\N	\N
461	10	40	159	least	\N	\N	\N	\N	\N	\N
462	11	43	171	least	\N	\N	\N	\N	\N	\N
463	11	42	167	least	\N	\N	\N	\N	\N	\N
464	11	42	166	most	\N	\N	\N	\N	\N	\N
465	10	39	154	most	\N	\N	\N	\N	\N	\N
466	10	39	156	least	\N	\N	\N	\N	\N	\N
467	11	41	163	most	\N	\N	\N	\N	\N	\N
468	11	41	164	least	\N	\N	\N	\N	\N	\N
469	11	40	157	most	\N	\N	\N	\N	\N	\N
470	11	40	159	least	\N	\N	\N	\N	\N	\N
472	10	38	151	most	\N	\N	\N	\N	\N	\N
525	10	25	100	most	\N	\N	\N	\N	\N	\N
473	11	39	153	least	\N	\N	\N	\N	\N	\N
526	10	25	98	least	\N	\N	\N	\N	\N	\N
527	11	25	98	least	\N	\N	\N	\N	\N	\N
528	11	25	100	most	\N	\N	\N	\N	\N	\N
529	12	43	169	most	\N	\N	\N	\N	\N	\N
474	11	39	154	most	\N	\N	\N	\N	\N	\N
530	12	43	170	least	\N	\N	\N	\N	\N	\N
475	11	38	150	most	\N	\N	\N	\N	\N	\N
531	12	35	137	most	\N	\N	\N	\N	\N	\N
476	11	38	149	least	\N	\N	\N	\N	\N	\N
471	10	38	150	least	\N	\N	\N	\N	\N	\N
477	11	37	145	most	\N	\N	\N	\N	\N	\N
478	11	37	146	least	\N	\N	\N	\N	\N	\N
482	11	36	142	most	\N	\N	\N	\N	\N	\N
481	11	36	144	least	\N	\N	\N	\N	\N	\N
479	10	37	146	least	\N	\N	\N	\N	\N	\N
480	10	37	145	most	\N	\N	\N	\N	\N	\N
483	11	35	140	most	\N	\N	\N	\N	\N	\N
484	11	35	138	least	\N	\N	\N	\N	\N	\N
486	10	36	142	most	\N	\N	\N	\N	\N	\N
487	11	34	133	least	\N	\N	\N	\N	\N	\N
488	11	34	136	most	\N	\N	\N	\N	\N	\N
485	10	36	144	least	\N	\N	\N	\N	\N	\N
489	10	35	137	most	\N	\N	\N	\N	\N	\N
491	10	35	139	least	\N	\N	\N	\N	\N	\N
492	10	34	136	most	\N	\N	\N	\N	\N	\N
532	12	35	138	least	\N	\N	\N	\N	\N	\N
493	10	34	135	least	\N	\N	\N	\N	\N	\N
533	12	47	185	most	\N	\N	\N	\N	\N	\N
534	12	47	186	least	\N	\N	\N	\N	\N	\N
535	12	32	125	most	\N	\N	\N	\N	\N	\N
536	12	32	126	least	\N	\N	\N	\N	\N	\N
495	10	33	129	least	\N	\N	\N	\N	\N	\N
537	12	29	113	most	\N	\N	\N	\N	\N	\N
538	12	29	114	least	\N	\N	\N	\N	\N	\N
496	10	33	131	most	\N	\N	\N	\N	\N	\N
539	12	31	121	most	\N	\N	\N	\N	\N	\N
490	11	33	131	most	\N	\N	\N	\N	\N	\N
494	11	33	130	least	\N	\N	\N	\N	\N	\N
497	10	32	126	most	\N	\N	\N	\N	\N	\N
498	10	32	127	least	\N	\N	\N	\N	\N	\N
499	11	32	125	least	\N	\N	\N	\N	\N	\N
500	11	32	127	most	\N	\N	\N	\N	\N	\N
501	11	31	122	least	\N	\N	\N	\N	\N	\N
502	10	31	122	least	\N	\N	\N	\N	\N	\N
503	10	31	124	most	\N	\N	\N	\N	\N	\N
504	11	31	124	most	\N	\N	\N	\N	\N	\N
505	11	30	118	least	\N	\N	\N	\N	\N	\N
506	10	30	117	most	\N	\N	\N	\N	\N	\N
507	10	30	118	least	\N	\N	\N	\N	\N	\N
509	10	29	115	most	\N	\N	\N	\N	\N	\N
510	10	29	114	least	\N	\N	\N	\N	\N	\N
508	11	30	117	most	\N	\N	\N	\N	\N	\N
511	11	29	115	most	\N	\N	\N	\N	\N	\N
513	10	28	110	most	\N	\N	\N	\N	\N	\N
514	11	29	113	least	\N	\N	\N	\N	\N	\N
515	11	28	109	least	\N	\N	\N	\N	\N	\N
516	11	28	112	most	\N	\N	\N	\N	\N	\N
512	10	28	111	least	\N	\N	\N	\N	\N	\N
517	11	27	108	most	\N	\N	\N	\N	\N	\N
518	11	27	106	least	\N	\N	\N	\N	\N	\N
520	10	27	107	least	\N	\N	\N	\N	\N	\N
522	11	26	103	least	\N	\N	\N	\N	\N	\N
519	10	27	108	most	\N	\N	\N	\N	\N	\N
521	11	26	104	most	\N	\N	\N	\N	\N	\N
523	10	26	102	most	\N	\N	\N	\N	\N	\N
524	10	26	101	least	\N	\N	\N	\N	\N	\N
540	12	31	122	least	\N	\N	\N	\N	\N	\N
541	12	34	133	most	\N	\N	\N	\N	\N	\N
542	12	34	134	least	\N	\N	\N	\N	\N	\N
543	12	44	173	most	\N	\N	\N	\N	\N	\N
544	12	33	130	least	\N	\N	\N	\N	\N	\N
545	12	33	129	most	\N	\N	\N	\N	\N	\N
546	12	44	174	least	\N	\N	\N	\N	\N	\N
547	12	27	106	most	\N	\N	\N	\N	\N	\N
548	12	27	105	least	\N	\N	\N	\N	\N	\N
549	12	46	183	most	\N	\N	\N	\N	\N	\N
550	12	46	182	least	\N	\N	\N	\N	\N	\N
551	12	37	147	most	\N	\N	\N	\N	\N	\N
552	12	37	146	least	\N	\N	\N	\N	\N	\N
553	12	36	144	most	\N	\N	\N	\N	\N	\N
554	12	36	143	least	\N	\N	\N	\N	\N	\N
555	12	25	99	least	\N	\N	\N	\N	\N	\N
556	12	25	100	most	\N	\N	\N	\N	\N	\N
557	12	48	189	most	\N	\N	\N	\N	\N	\N
558	12	48	190	least	\N	\N	\N	\N	\N	\N
559	12	45	177	most	\N	\N	\N	\N	\N	\N
560	12	45	179	least	\N	\N	\N	\N	\N	\N
561	12	42	165	most	\N	\N	\N	\N	\N	\N
562	12	42	167	least	\N	\N	\N	\N	\N	\N
563	12	28	112	most	\N	\N	\N	\N	\N	\N
564	12	28	110	least	\N	\N	\N	\N	\N	\N
565	12	40	157	most	\N	\N	\N	\N	\N	\N
566	12	40	159	least	\N	\N	\N	\N	\N	\N
567	12	41	161	most	\N	\N	\N	\N	\N	\N
568	12	41	164	least	\N	\N	\N	\N	\N	\N
569	12	39	155	most	\N	\N	\N	\N	\N	\N
570	12	39	154	least	\N	\N	\N	\N	\N	\N
571	12	38	149	most	\N	\N	\N	\N	\N	\N
572	12	38	150	least	\N	\N	\N	\N	\N	\N
573	12	26	104	most	\N	\N	\N	\N	\N	\N
574	12	26	102	least	\N	\N	\N	\N	\N	\N
575	12	30	117	most	\N	\N	\N	\N	\N	\N
576	12	30	120	least	\N	\N	\N	\N	\N	\N
577	13	48	190	most	\N	\N	\N	\N	\N	\N
580	14	48	192	least	\N	\N	\N	\N	\N	\N
579	13	47	188	most	\N	\N	\N	\N	\N	\N
654	13	25	98	least	\N	\N	\N	\N	\N	\N
581	13	46	182	most	\N	\N	\N	\N	\N	\N
578	14	48	190	most	\N	\N	\N	\N	\N	\N
582	13	45	180	most	\N	\N	\N	\N	\N	\N
583	13	44	175	most	\N	\N	\N	\N	\N	\N
584	14	47	185	least	\N	\N	\N	\N	\N	\N
585	14	47	186	most	\N	\N	\N	\N	\N	\N
586	13	43	172	most	\N	\N	\N	\N	\N	\N
587	14	46	183	most	\N	\N	\N	\N	\N	\N
588	13	42	166	most	\N	\N	\N	\N	\N	\N
589	14	46	182	least	\N	\N	\N	\N	\N	\N
591	13	41	163	least	\N	\N	\N	\N	\N	\N
592	14	45	178	most	\N	\N	\N	\N	\N	\N
593	14	45	177	least	\N	\N	\N	\N	\N	\N
590	13	41	164	most	\N	\N	\N	\N	\N	\N
594	14	44	175	most	\N	\N	\N	\N	\N	\N
595	13	48	191	least	\N	\N	\N	\N	\N	\N
596	14	44	174	least	\N	\N	\N	\N	\N	\N
597	13	47	187	least	\N	\N	\N	\N	\N	\N
598	13	46	184	least	\N	\N	\N	\N	\N	\N
599	14	43	170	least	\N	\N	\N	\N	\N	\N
600	14	43	172	most	\N	\N	\N	\N	\N	\N
601	13	45	178	least	\N	\N	\N	\N	\N	\N
602	13	44	173	least	\N	\N	\N	\N	\N	\N
603	14	42	167	least	\N	\N	\N	\N	\N	\N
604	13	43	169	least	\N	\N	\N	\N	\N	\N
605	14	42	165	most	\N	\N	\N	\N	\N	\N
606	13	42	168	least	\N	\N	\N	\N	\N	\N
607	13	40	157	least	\N	\N	\N	\N	\N	\N
608	13	40	158	most	\N	\N	\N	\N	\N	\N
609	14	41	164	least	\N	\N	\N	\N	\N	\N
610	14	41	162	most	\N	\N	\N	\N	\N	\N
612	13	39	156	least	\N	\N	\N	\N	\N	\N
613	14	40	159	least	\N	\N	\N	\N	\N	\N
611	13	39	155	most	\N	\N	\N	\N	\N	\N
614	14	40	160	most	\N	\N	\N	\N	\N	\N
615	13	38	151	most	\N	\N	\N	\N	\N	\N
616	13	38	149	least	\N	\N	\N	\N	\N	\N
617	13	37	145	most	\N	\N	\N	\N	\N	\N
618	13	37	146	least	\N	\N	\N	\N	\N	\N
619	14	39	153	least	\N	\N	\N	\N	\N	\N
621	13	36	142	most	\N	\N	\N	\N	\N	\N
620	13	36	144	least	\N	\N	\N	\N	\N	\N
622	14	39	155	most	\N	\N	\N	\N	\N	\N
623	13	35	137	most	\N	\N	\N	\N	\N	\N
624	13	35	138	least	\N	\N	\N	\N	\N	\N
625	14	38	150	least	\N	\N	\N	\N	\N	\N
626	14	38	152	most	\N	\N	\N	\N	\N	\N
627	13	34	133	least	\N	\N	\N	\N	\N	\N
628	13	34	136	most	\N	\N	\N	\N	\N	\N
629	14	37	147	least	\N	\N	\N	\N	\N	\N
630	14	37	145	most	\N	\N	\N	\N	\N	\N
631	13	33	131	most	\N	\N	\N	\N	\N	\N
633	13	33	129	least	\N	\N	\N	\N	\N	\N
632	14	36	144	least	\N	\N	\N	\N	\N	\N
634	13	32	125	most	\N	\N	\N	\N	\N	\N
635	14	36	142	most	\N	\N	\N	\N	\N	\N
636	13	32	128	least	\N	\N	\N	\N	\N	\N
637	13	31	122	least	\N	\N	\N	\N	\N	\N
638	13	31	124	most	\N	\N	\N	\N	\N	\N
639	13	30	117	most	\N	\N	\N	\N	\N	\N
640	13	30	118	least	\N	\N	\N	\N	\N	\N
641	13	29	113	most	\N	\N	\N	\N	\N	\N
642	13	29	115	least	\N	\N	\N	\N	\N	\N
643	14	35	140	least	\N	\N	\N	\N	\N	\N
644	13	28	109	least	\N	\N	\N	\N	\N	\N
645	13	28	111	most	\N	\N	\N	\N	\N	\N
646	13	27	105	most	\N	\N	\N	\N	\N	\N
647	13	27	106	least	\N	\N	\N	\N	\N	\N
648	14	35	139	most	\N	\N	\N	\N	\N	\N
649	13	26	103	most	\N	\N	\N	\N	\N	\N
650	13	26	101	least	\N	\N	\N	\N	\N	\N
651	14	34	135	least	\N	\N	\N	\N	\N	\N
652	14	34	136	most	\N	\N	\N	\N	\N	\N
653	13	25	100	most	\N	\N	\N	\N	\N	\N
655	14	33	130	most	\N	\N	\N	\N	\N	\N
656	14	33	132	least	\N	\N	\N	\N	\N	\N
657	14	32	125	least	\N	\N	\N	\N	\N	\N
658	14	32	127	most	\N	\N	\N	\N	\N	\N
659	14	31	122	least	\N	\N	\N	\N	\N	\N
660	14	31	124	most	\N	\N	\N	\N	\N	\N
661	14	30	118	least	\N	\N	\N	\N	\N	\N
662	14	30	117	most	\N	\N	\N	\N	\N	\N
663	14	29	114	least	\N	\N	\N	\N	\N	\N
664	14	29	115	most	\N	\N	\N	\N	\N	\N
665	14	28	109	least	\N	\N	\N	\N	\N	\N
666	14	28	112	most	\N	\N	\N	\N	\N	\N
667	14	27	107	least	\N	\N	\N	\N	\N	\N
668	14	27	108	most	\N	\N	\N	\N	\N	\N
669	14	26	101	least	\N	\N	\N	\N	\N	\N
670	14	26	102	most	\N	\N	\N	\N	\N	\N
671	14	25	97	least	\N	\N	\N	\N	\N	\N
672	14	25	99	most	\N	\N	\N	\N	\N	\N
673	15	48	192	most	\N	\N	\N	\N	\N	\N
674	15	48	190	least	\N	\N	\N	\N	\N	\N
675	15	46	183	most	\N	\N	\N	\N	\N	\N
676	15	46	184	least	\N	\N	\N	\N	\N	\N
677	15	45	177	least	\N	\N	\N	\N	\N	\N
678	15	45	178	most	\N	\N	\N	\N	\N	\N
679	15	44	173	most	\N	\N	\N	\N	\N	\N
680	15	44	176	least	\N	\N	\N	\N	\N	\N
681	15	43	171	least	\N	\N	\N	\N	\N	\N
682	15	43	172	most	\N	\N	\N	\N	\N	\N
683	15	42	165	most	\N	\N	\N	\N	\N	\N
684	15	42	167	least	\N	\N	\N	\N	\N	\N
685	15	41	161	most	\N	\N	\N	\N	\N	\N
686	15	41	164	least	\N	\N	\N	\N	\N	\N
687	15	40	158	most	\N	\N	\N	\N	\N	\N
688	15	40	159	least	\N	\N	\N	\N	\N	\N
689	15	39	153	least	\N	\N	\N	\N	\N	\N
690	15	39	154	most	\N	\N	\N	\N	\N	\N
691	15	38	150	least	\N	\N	\N	\N	\N	\N
757	16	30	120	most	\N	\N	\N	\N	\N	\N
692	15	38	152	most	\N	\N	\N	\N	\N	\N
693	15	37	145	most	\N	\N	\N	\N	\N	\N
694	15	37	146	least	\N	\N	\N	\N	\N	\N
696	15	36	142	most	\N	\N	\N	\N	\N	\N
695	15	36	144	least	\N	\N	\N	\N	\N	\N
697	15	35	138	least	\N	\N	\N	\N	\N	\N
698	15	35	140	most	\N	\N	\N	\N	\N	\N
699	15	34	133	least	\N	\N	\N	\N	\N	\N
700	15	34	136	most	\N	\N	\N	\N	\N	\N
701	15	33	130	most	\N	\N	\N	\N	\N	\N
702	15	33	131	least	\N	\N	\N	\N	\N	\N
703	15	32	127	most	\N	\N	\N	\N	\N	\N
704	15	32	125	least	\N	\N	\N	\N	\N	\N
705	15	31	122	least	\N	\N	\N	\N	\N	\N
706	15	31	124	most	\N	\N	\N	\N	\N	\N
707	15	30	117	most	\N	\N	\N	\N	\N	\N
708	15	30	118	least	\N	\N	\N	\N	\N	\N
709	15	29	114	most	\N	\N	\N	\N	\N	\N
710	15	29	115	least	\N	\N	\N	\N	\N	\N
711	15	28	109	least	\N	\N	\N	\N	\N	\N
712	15	28	111	most	\N	\N	\N	\N	\N	\N
713	15	27	108	most	\N	\N	\N	\N	\N	\N
714	15	27	107	least	\N	\N	\N	\N	\N	\N
715	15	26	101	least	\N	\N	\N	\N	\N	\N
716	15	26	102	most	\N	\N	\N	\N	\N	\N
717	15	47	186	most	\N	\N	\N	\N	\N	\N
718	15	47	187	least	\N	\N	\N	\N	\N	\N
720	15	25	99	most	\N	\N	\N	\N	\N	\N
719	15	25	98	least	\N	\N	\N	\N	\N	\N
721	16	48	189	most	\N	\N	\N	\N	\N	\N
722	16	48	190	least	\N	\N	\N	\N	\N	\N
723	16	47	185	least	\N	\N	\N	\N	\N	\N
724	16	47	187	most	\N	\N	\N	\N	\N	\N
725	16	46	181	least	\N	\N	\N	\N	\N	\N
726	16	46	184	most	\N	\N	\N	\N	\N	\N
727	16	45	177	least	\N	\N	\N	\N	\N	\N
728	16	45	180	most	\N	\N	\N	\N	\N	\N
729	16	44	176	most	\N	\N	\N	\N	\N	\N
759	16	29	113	least	\N	\N	\N	\N	\N	\N
730	16	44	173	least	\N	\N	\N	\N	\N	\N
731	16	43	171	least	\N	\N	\N	\N	\N	\N
732	16	43	172	most	\N	\N	\N	\N	\N	\N
733	16	42	165	least	\N	\N	\N	\N	\N	\N
734	16	42	168	most	\N	\N	\N	\N	\N	\N
735	16	41	161	most	\N	\N	\N	\N	\N	\N
736	16	41	164	least	\N	\N	\N	\N	\N	\N
737	16	40	157	least	\N	\N	\N	\N	\N	\N
738	16	40	158	most	\N	\N	\N	\N	\N	\N
739	16	39	153	least	\N	\N	\N	\N	\N	\N
740	16	39	154	most	\N	\N	\N	\N	\N	\N
741	16	38	150	least	\N	\N	\N	\N	\N	\N
742	16	38	149	most	\N	\N	\N	\N	\N	\N
743	16	37	145	most	\N	\N	\N	\N	\N	\N
760	16	29	115	most	\N	\N	\N	\N	\N	\N
744	16	37	146	least	\N	\N	\N	\N	\N	\N
745	16	36	141	least	\N	\N	\N	\N	\N	\N
746	16	36	142	most	\N	\N	\N	\N	\N	\N
747	16	35	140	least	\N	\N	\N	\N	\N	\N
748	16	35	137	most	\N	\N	\N	\N	\N	\N
749	16	34	134	least	\N	\N	\N	\N	\N	\N
750	16	34	135	most	\N	\N	\N	\N	\N	\N
751	16	33	130	most	\N	\N	\N	\N	\N	\N
752	16	33	132	least	\N	\N	\N	\N	\N	\N
753	16	32	125	least	\N	\N	\N	\N	\N	\N
754	16	32	127	most	\N	\N	\N	\N	\N	\N
755	16	31	122	least	\N	\N	\N	\N	\N	\N
756	16	31	124	most	\N	\N	\N	\N	\N	\N
758	16	30	118	least	\N	\N	\N	\N	\N	\N
761	16	28	109	least	\N	\N	\N	\N	\N	\N
762	16	28	110	most	\N	\N	\N	\N	\N	\N
763	16	27	105	least	\N	\N	\N	\N	\N	\N
764	16	27	108	most	\N	\N	\N	\N	\N	\N
765	16	26	103	least	\N	\N	\N	\N	\N	\N
766	16	26	104	most	\N	\N	\N	\N	\N	\N
768	16	25	100	most	\N	\N	\N	\N	\N	\N
767	16	25	98	least	\N	\N	\N	\N	\N	\N
770	17	34	133	least	\N	\N	\N	\N	\N	\N
769	17	34	136	most	\N	\N	\N	\N	\N	\N
771	17	32	127	most	\N	\N	\N	\N	\N	\N
772	17	32	128	least	\N	\N	\N	\N	\N	\N
773	17	37	148	most	\N	\N	\N	\N	\N	\N
774	17	37	146	least	\N	\N	\N	\N	\N	\N
775	17	44	173	most	\N	\N	\N	\N	\N	\N
776	17	44	175	least	\N	\N	\N	\N	\N	\N
777	17	27	105	most	\N	\N	\N	\N	\N	\N
778	17	27	106	least	\N	\N	\N	\N	\N	\N
779	17	38	150	least	\N	\N	\N	\N	\N	\N
780	17	38	149	most	\N	\N	\N	\N	\N	\N
781	17	29	113	least	\N	\N	\N	\N	\N	\N
782	17	29	114	most	\N	\N	\N	\N	\N	\N
783	17	28	109	least	\N	\N	\N	\N	\N	\N
784	17	28	110	most	\N	\N	\N	\N	\N	\N
785	17	43	171	least	\N	\N	\N	\N	\N	\N
786	17	43	169	most	\N	\N	\N	\N	\N	\N
787	17	41	161	most	\N	\N	\N	\N	\N	\N
788	17	41	162	least	\N	\N	\N	\N	\N	\N
853	18	85	266	\N	\N	0.00	\N	\N	\N	\N
789	17	36	144	least	\N	\N	\N	\N	\N	\N
790	17	36	142	most	\N	\N	\N	\N	\N	\N
791	17	30	118	least	\N	\N	\N	\N	\N	\N
792	17	30	117	most	\N	\N	\N	\N	\N	\N
793	17	45	179	most	\N	\N	\N	\N	\N	\N
794	17	45	177	least	\N	\N	\N	\N	\N	\N
795	17	48	189	most	\N	\N	\N	\N	\N	\N
796	17	48	192	least	\N	\N	\N	\N	\N	\N
797	17	47	185	least	\N	\N	\N	\N	\N	\N
798	17	47	188	most	\N	\N	\N	\N	\N	\N
800	17	33	131	least	\N	\N	\N	\N	\N	\N
799	17	33	130	most	\N	\N	\N	\N	\N	\N
802	17	46	183	most	\N	\N	\N	\N	\N	\N
801	17	46	182	least	\N	\N	\N	\N	\N	\N
803	17	31	122	least	\N	\N	\N	\N	\N	\N
804	17	31	124	most	\N	\N	\N	\N	\N	\N
805	17	40	158	most	\N	\N	\N	\N	\N	\N
806	17	40	159	least	\N	\N	\N	\N	\N	\N
807	17	35	140	least	\N	\N	\N	\N	\N	\N
808	17	35	137	most	\N	\N	\N	\N	\N	\N
809	17	39	154	most	\N	\N	\N	\N	\N	\N
810	17	39	156	least	\N	\N	\N	\N	\N	\N
811	17	26	102	most	\N	\N	\N	\N	\N	\N
812	17	26	103	least	\N	\N	\N	\N	\N	\N
813	17	42	165	most	\N	\N	\N	\N	\N	\N
814	17	42	167	least	\N	\N	\N	\N	\N	\N
816	17	25	98	least	\N	\N	\N	\N	\N	\N
815	17	25	100	most	\N	\N	\N	\N	\N	\N
817	18	49	193	\N	\N	0.00	\N	\N	\N	\N
818	18	50	195	\N	\N	0.00	\N	\N	\N	\N
819	18	51	197	\N	\N	0.00	\N	\N	\N	\N
820	18	52	199	\N	\N	0.00	\N	\N	\N	\N
821	18	53	201	\N	\N	0.00	\N	\N	\N	\N
822	18	54	203	\N	\N	0.00	\N	\N	\N	\N
823	18	55	205	\N	\N	0.00	\N	\N	\N	\N
824	18	56	207	\N	\N	0.00	\N	\N	\N	\N
825	18	57	209	\N	\N	0.00	\N	\N	\N	\N
826	18	58	211	\N	\N	0.00	\N	\N	\N	\N
827	18	59	213	\N	\N	0.00	\N	\N	\N	\N
828	18	60	215	\N	\N	0.00	\N	\N	\N	\N
829	18	61	217	\N	\N	0.00	\N	\N	\N	\N
830	18	62	219	\N	\N	0.00	\N	\N	\N	\N
831	18	63	221	\N	\N	0.00	\N	\N	\N	\N
832	18	64	223	\N	\N	0.00	\N	\N	\N	\N
833	18	65	225	\N	\N	0.00	\N	\N	\N	\N
834	18	66	227	\N	\N	0.00	\N	\N	\N	\N
835	18	67	229	\N	\N	0.00	\N	\N	\N	\N
836	18	68	231	\N	\N	0.00	\N	\N	\N	\N
837	18	69	234	\N	\N	0.00	\N	\N	\N	\N
838	18	70	236	\N	\N	0.00	\N	\N	\N	\N
839	18	71	238	\N	\N	0.00	\N	\N	\N	\N
840	18	72	240	\N	\N	0.00	\N	\N	\N	\N
841	18	73	242	\N	\N	0.00	\N	\N	\N	\N
842	18	74	244	\N	\N	0.00	\N	\N	\N	\N
843	18	75	246	\N	\N	0.00	\N	\N	\N	\N
844	18	76	248	\N	\N	0.00	\N	\N	\N	\N
845	18	77	250	\N	\N	0.00	\N	\N	\N	\N
846	18	78	252	\N	\N	0.00	\N	\N	\N	\N
847	18	79	254	\N	\N	0.00	\N	\N	\N	\N
848	18	80	256	\N	\N	0.00	\N	\N	\N	\N
849	18	81	258	\N	\N	0.00	\N	\N	\N	\N
850	18	82	260	\N	\N	0.00	\N	\N	\N	\N
851	18	83	262	\N	\N	0.00	\N	\N	\N	\N
852	18	84	264	\N	\N	0.00	\N	\N	\N	\N
854	18	86	268	\N	\N	0.00	\N	\N	\N	\N
855	18	87	270	\N	\N	0.00	\N	\N	\N	\N
856	18	88	272	\N	\N	0.00	\N	\N	\N	\N
857	18	89	273	\N	\N	0.00	\N	\N	\N	\N
858	18	90	275	\N	\N	0.00	\N	\N	\N	\N
859	18	91	277	\N	\N	0.00	\N	\N	\N	\N
860	18	92	279	\N	\N	0.00	\N	\N	\N	\N
861	18	93	281	\N	\N	0.00	\N	\N	\N	\N
862	18	94	283	\N	\N	0.00	\N	\N	\N	\N
863	18	95	285	\N	\N	0.00	\N	\N	\N	\N
864	18	96	287	\N	\N	0.00	\N	\N	\N	\N
865	18	97	289	\N	\N	0.00	\N	\N	\N	\N
866	18	98	291	\N	\N	0.00	\N	\N	\N	\N
867	18	99	293	\N	\N	0.00	\N	\N	\N	\N
868	18	100	295	\N	\N	0.00	\N	\N	\N	\N
874	18	106	307	\N	\N	0.00	\N	\N	\N	\N
869	18	101	297	\N	\N	0.00	\N	\N	\N	\N
870	18	102	299	\N	\N	0.00	\N	\N	\N	\N
871	18	103	301	\N	\N	0.00	\N	\N	\N	\N
872	18	104	303	\N	\N	0.00	\N	\N	\N	\N
873	18	105	305	\N	\N	0.00	\N	\N	\N	\N
875	18	107	309	\N	\N	0.00	\N	\N	\N	\N
876	18	108	311	\N	\N	0.00	\N	\N	\N	\N
877	18	109	314	\N	\N	0.00	\N	\N	\N	\N
878	18	110	316	\N	\N	0.00	\N	\N	\N	\N
879	18	111	318	\N	\N	0.00	\N	\N	\N	\N
880	18	112	320	\N	\N	0.00	\N	\N	\N	\N
881	18	113	322	\N	\N	0.00	\N	\N	\N	\N
882	18	114	324	\N	\N	0.00	\N	\N	\N	\N
883	18	115	326	\N	\N	0.00	\N	\N	\N	\N
884	18	116	328	\N	\N	0.00	\N	\N	\N	\N
885	18	117	330	\N	\N	0.00	\N	\N	\N	\N
886	18	118	332	\N	\N	0.00	\N	\N	\N	\N
887	18	119	334	\N	\N	0.00	\N	\N	\N	\N
888	18	120	336	\N	\N	0.00	\N	\N	\N	\N
889	18	121	338	\N	\N	0.00	\N	\N	\N	\N
890	18	122	340	\N	\N	0.00	\N	\N	\N	\N
891	18	123	342	\N	\N	0.00	\N	\N	\N	\N
892	18	124	344	\N	\N	0.00	\N	\N	\N	\N
893	18	125	346	\N	\N	0.00	\N	\N	\N	\N
894	18	126	348	\N	\N	0.00	\N	\N	\N	\N
895	18	127	350	\N	\N	0.00	\N	\N	\N	\N
896	18	128	352	\N	\N	0.00	\N	\N	\N	\N
897	18	129	354	\N	\N	0.00	\N	\N	\N	\N
898	18	130	356	\N	\N	0.00	\N	\N	\N	\N
899	18	131	358	\N	\N	0.00	\N	\N	\N	\N
900	18	132	360	\N	\N	0.00	\N	\N	\N	\N
901	18	133	362	\N	\N	0.00	\N	\N	\N	\N
902	18	134	364	\N	\N	0.00	\N	\N	\N	\N
903	18	135	366	\N	\N	0.00	\N	\N	\N	\N
904	18	136	368	\N	\N	0.00	\N	\N	\N	\N
905	18	137	370	\N	\N	0.00	\N	\N	\N	\N
906	18	138	372	\N	\N	0.00	\N	\N	\N	\N
907	19	49	193	\N	\N	0.00	\N	\N	\N	\N
908	19	50	195	\N	\N	0.00	\N	\N	\N	\N
909	19	51	198	\N	\N	0.00	\N	\N	\N	\N
910	19	52	200	\N	\N	0.00	\N	\N	\N	\N
911	19	53	201	\N	\N	0.00	\N	\N	\N	\N
912	19	54	203	\N	\N	0.00	\N	\N	\N	\N
913	19	55	206	\N	\N	0.00	\N	\N	\N	\N
914	19	56	208	\N	\N	0.00	\N	\N	\N	\N
915	19	57	209	\N	\N	0.00	\N	\N	\N	\N
916	19	58	212	\N	\N	0.00	\N	\N	\N	\N
917	19	59	214	\N	\N	0.00	\N	\N	\N	\N
918	19	60	215	\N	\N	0.00	\N	\N	\N	\N
919	19	61	218	\N	\N	0.00	\N	\N	\N	\N
920	19	62	219	\N	\N	0.00	\N	\N	\N	\N
921	19	63	221	\N	\N	0.00	\N	\N	\N	\N
922	19	64	224	\N	\N	0.00	\N	\N	\N	\N
923	19	65	225	\N	\N	0.00	\N	\N	\N	\N
924	19	66	228	\N	\N	0.00	\N	\N	\N	\N
925	19	67	229	\N	\N	0.00	\N	\N	\N	\N
926	19	68	232	\N	\N	0.00	\N	\N	\N	\N
927	19	69	233	\N	\N	0.00	\N	\N	\N	\N
928	19	70	236	\N	\N	0.00	\N	\N	\N	\N
929	19	71	238	\N	\N	0.00	\N	\N	\N	\N
930	19	72	239	\N	\N	0.00	\N	\N	\N	\N
931	19	73	242	\N	\N	0.00	\N	\N	\N	\N
932	19	74	244	\N	\N	0.00	\N	\N	\N	\N
933	19	75	245	\N	\N	0.00	\N	\N	\N	\N
934	19	76	247	\N	\N	0.00	\N	\N	\N	\N
935	19	77	250	\N	\N	0.00	\N	\N	\N	\N
936	19	78	252	\N	\N	0.00	\N	\N	\N	\N
937	19	79	253	\N	\N	0.00	\N	\N	\N	\N
938	19	80	256	\N	\N	0.00	\N	\N	\N	\N
939	19	81	258	\N	\N	0.00	\N	\N	\N	\N
940	19	82	259	\N	\N	0.00	\N	\N	\N	\N
941	19	83	262	\N	\N	0.00	\N	\N	\N	\N
942	19	84	264	\N	\N	0.00	\N	\N	\N	\N
943	19	85	265	\N	\N	0.00	\N	\N	\N	\N
944	19	86	268	\N	\N	0.00	\N	\N	\N	\N
945	19	87	269	\N	\N	0.00	\N	\N	\N	\N
946	19	88	272	\N	\N	0.00	\N	\N	\N	\N
947	19	89	273	\N	\N	0.00	\N	\N	\N	\N
948	19	90	275	\N	\N	0.00	\N	\N	\N	\N
949	19	91	277	\N	\N	0.00	\N	\N	\N	\N
950	19	92	280	\N	\N	0.00	\N	\N	\N	\N
951	19	93	282	\N	\N	0.00	\N	\N	\N	\N
952	19	94	283	\N	\N	0.00	\N	\N	\N	\N
953	19	95	285	\N	\N	0.00	\N	\N	\N	\N
954	19	96	287	\N	\N	0.00	\N	\N	\N	\N
955	19	97	290	\N	\N	0.00	\N	\N	\N	\N
956	19	98	292	\N	\N	0.00	\N	\N	\N	\N
957	19	99	293	\N	\N	0.00	\N	\N	\N	\N
958	19	100	296	\N	\N	0.00	\N	\N	\N	\N
959	19	101	298	\N	\N	0.00	\N	\N	\N	\N
960	19	102	299	\N	\N	0.00	\N	\N	\N	\N
961	19	103	301	\N	\N	0.00	\N	\N	\N	\N
962	19	104	304	\N	\N	0.00	\N	\N	\N	\N
963	19	105	306	\N	\N	0.00	\N	\N	\N	\N
964	19	106	307	\N	\N	0.00	\N	\N	\N	\N
965	19	107	310	\N	\N	0.00	\N	\N	\N	\N
966	19	108	312	\N	\N	0.00	\N	\N	\N	\N
967	19	109	313	\N	\N	0.00	\N	\N	\N	\N
968	19	110	315	\N	\N	0.00	\N	\N	\N	\N
969	19	111	318	\N	\N	0.00	\N	\N	\N	\N
970	19	112	319	\N	\N	0.00	\N	\N	\N	\N
971	19	113	322	\N	\N	0.00	\N	\N	\N	\N
972	19	114	324	\N	\N	0.00	\N	\N	\N	\N
973	19	115	325	\N	\N	0.00	\N	\N	\N	\N
974	19	116	327	\N	\N	0.00	\N	\N	\N	\N
975	19	117	329	\N	\N	0.00	\N	\N	\N	\N
976	19	118	332	\N	\N	0.00	\N	\N	\N	\N
977	19	119	333	\N	\N	0.00	\N	\N	\N	\N
978	19	120	336	\N	\N	0.00	\N	\N	\N	\N
979	19	121	338	\N	\N	0.00	\N	\N	\N	\N
980	19	122	340	\N	\N	0.00	\N	\N	\N	\N
981	19	123	341	\N	\N	0.00	\N	\N	\N	\N
982	19	124	343	\N	\N	0.00	\N	\N	\N	\N
983	19	125	346	\N	\N	0.00	\N	\N	\N	\N
984	19	126	348	\N	\N	0.00	\N	\N	\N	\N
985	19	127	349	\N	\N	0.00	\N	\N	\N	\N
986	19	128	351	\N	\N	0.00	\N	\N	\N	\N
987	19	129	353	\N	\N	0.00	\N	\N	\N	\N
988	19	130	355	\N	\N	0.00	\N	\N	\N	\N
989	19	131	358	\N	\N	0.00	\N	\N	\N	\N
990	19	132	359	\N	\N	0.00	\N	\N	\N	\N
991	19	133	361	\N	\N	0.00	\N	\N	\N	\N
992	19	134	364	\N	\N	0.00	\N	\N	\N	\N
993	19	135	365	\N	\N	0.00	\N	\N	\N	\N
994	19	136	368	\N	\N	0.00	\N	\N	\N	\N
995	19	137	370	\N	\N	0.00	\N	\N	\N	\N
996	19	138	371	\N	\N	0.00	\N	\N	\N	\N
\.


--
-- Data for Name: test_attempts; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.test_attempts (id, job_application_id, test_id, started_at, finished_at, duration, objective_score, essay_score, total_score, status, user_id, attempt_type, participant_name, participant_age, participant_gender, test_date) FROM stdin;
2	3	3	2026-08-26 21:35:02	2026-08-26 21:37:52	171	0.00	0.00	0.00	completed	\N	applicant	\N	\N	\N	\N
1	2	2	2026-08-26 20:59:53	2026-08-26 21:06:25	393	0.00	0.00	0.00	completed	\N	applicant	\N	\N	\N	\N
3	1	1	2026-08-27 09:53:46	2026-08-27 10:00:43	418	0.00	0.00	0.00	completed	\N	applicant	\N	\N	\N	\N
4	4	4	2026-08-27 10:07:52	2026-08-27 10:12:31	280	0.00	\N	100.00	completed	\N	applicant	\N	\N	\N	\N
5	\N	5	2026-09-01 10:45:47	2026-09-01 10:47:42	115	0.00	\N	100.00	completed	5	employee	Syauqi Maul	50	male	2026-09-01
6	\N	5	2026-09-01 11:10:35	2026-09-01 11:18:56	501	0.00	\N	100.00	completed	6	employee	DWI LESTARI INDAH SARI	24	female	2026-09-01
9	\N	5	2026-09-01 11:38:51	2026-09-01 11:40:37	107	0.00	\N	100.00	completed	2	employee	Ilham Taruprasetyo	22	male	2026-09-01
10	\N	5	2026-09-01 11:43:27	2026-09-01 11:51:21	475	0.00	\N	100.00	completed	9	employee	Dita Tri Handayani	21	female	2026-09-01
11	\N	5	2026-09-01 11:44:33	2026-09-01 11:51:42	429	0.00	\N	100.00	completed	10	employee	Hasna Nur Aisyah Makarim	21	female	2026-09-01
12	5	2	2026-09-01 15:12:13	2026-09-01 15:14:09	116	0.00	\N	100.00	completed	\N	applicant	\N	\N	\N	\N
14	\N	5	2026-09-02 10:33:11	2026-09-02 10:43:09	598	0.00	\N	100.00	completed	11	employee	Galuh Prasetya Ningrum	28	female	2026-09-02
13	\N	5	2026-09-02 10:33:03	2026-09-02 10:40:22	439	0.00	0.00	0.00	completed	12	employee	Anggun Widiasari	24	female	2026-09-02
15	\N	5	2026-09-02 12:32:01	2026-09-02 12:37:57	357	0.00	\N	100.00	completed	14	employee	Samsul Hidayat	28	male	2026-09-02
16	\N	5	2026-09-02 15:11:55	2026-09-02 15:18:22	387	0.00	\N	100.00	completed	15	employee	Faisha Sitohang	25	male	2026-09-02
17	6	1	2026-09-04 10:54:43	2026-09-04 10:59:24	281	0.00	\N	100.00	completed	\N	applicant	\N	\N	\N	\N
18	\N	7	2026-09-24 08:54:31	2026-09-24 08:58:24	234	0.00	\N	100.00	completed	2	employee	Ilham Taruprasetyo	22	male	2026-09-24
19	\N	7	2026-09-25 08:44:55	2026-09-25 08:52:54	479	0.00	\N	100.00	completed	5	employee	Syauqi Maul	24	male	2026-09-25
\.


--
-- Data for Name: test_categories; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.test_categories (id, name, description, created_at, updated_at) FROM stdin;
2	Tes Kepribadian (DISC)	Tes profil kepribadian DISC (Dominance, Influence, Steadiness, Compliance) 24 Nomor.	2026-08-26 13:52:49	2026-08-26 13:52:49
3	Tes Kepribadian (PAPI Kostick)	Tes profil kepribadian PAPI Kostick (Personality and Preference Inventory) 90 Pasang Pernyataan.	2026-09-24 08:19:21	2026-09-24 08:19:21
\.


--
-- Data for Name: test_questions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.test_questions (id, test_id, question_id, order_number) FROM stdin;
97	5	48	1
98	5	47	2
99	5	46	3
100	5	45	4
101	5	44	5
102	5	43	6
103	5	42	7
104	5	41	8
105	5	40	9
106	5	39	10
107	5	38	11
108	5	37	12
109	5	36	13
110	5	35	14
111	5	34	15
112	5	33	16
113	5	32	17
114	5	31	18
115	5	30	19
116	5	29	20
117	5	28	21
118	5	27	22
119	5	26	23
120	5	25	24
49	3	48	1
50	3	47	2
51	3	46	3
52	3	45	4
53	3	44	5
54	3	43	6
55	3	42	7
56	3	41	8
57	3	40	9
58	3	39	10
59	3	38	11
60	3	37	12
61	3	36	13
62	3	35	14
63	3	34	15
64	3	33	16
65	3	32	17
66	3	31	18
67	3	30	19
68	3	29	20
69	3	28	21
70	3	27	22
71	3	26	23
72	3	25	24
73	4	48	1
74	4	47	2
75	4	46	3
76	4	45	4
77	4	44	5
78	4	43	6
79	4	42	7
80	4	41	8
81	4	40	9
82	4	39	10
83	4	38	11
84	4	37	12
85	4	36	13
86	4	35	14
87	4	34	15
88	4	33	16
89	4	32	17
90	4	31	18
91	4	30	19
92	4	29	20
93	4	28	21
94	4	27	22
95	4	26	23
96	4	25	24
145	1	48	1
146	1	47	2
147	1	46	3
148	1	45	4
149	1	44	5
150	1	43	6
151	1	42	7
152	1	41	8
153	1	40	9
154	1	39	10
155	1	38	11
156	1	37	12
157	1	36	13
158	1	35	14
159	1	34	15
160	1	33	16
161	1	32	17
162	1	31	18
163	1	30	19
164	1	29	20
165	1	28	21
166	1	27	22
167	1	26	23
121	6	48	1
168	1	25	24
122	6	47	2
123	6	46	3
124	6	45	4
125	6	44	5
126	6	43	6
127	6	42	7
128	6	41	8
129	6	40	9
130	6	39	10
131	6	38	11
132	6	37	12
133	6	36	13
134	6	35	14
135	6	34	15
136	6	33	16
137	6	32	17
138	6	31	18
139	6	30	19
140	6	29	20
141	6	28	21
142	6	27	22
143	6	26	23
144	6	25	24
169	7	49	1
170	7	50	2
171	7	51	3
172	7	52	4
173	7	53	5
174	7	54	6
175	7	55	7
176	7	56	8
177	7	57	9
178	7	58	10
179	7	59	11
180	7	60	12
181	7	61	13
182	7	62	14
183	7	63	15
184	7	64	16
185	7	65	17
186	7	66	18
187	7	67	19
188	7	68	20
189	7	69	21
190	7	70	22
191	7	71	23
192	7	72	24
193	7	73	25
194	7	74	26
195	7	75	27
196	7	76	28
197	7	77	29
198	7	78	30
199	7	79	31
200	7	80	32
201	7	81	33
202	7	82	34
203	7	83	35
204	7	84	36
205	7	85	37
206	7	86	38
207	7	87	39
208	7	88	40
209	7	89	41
210	7	90	42
211	7	91	43
212	7	92	44
213	7	93	45
214	7	94	46
215	7	95	47
216	7	96	48
217	7	97	49
218	7	98	50
219	7	99	51
220	7	100	52
221	7	101	53
222	7	102	54
223	7	103	55
224	7	104	56
225	7	105	57
226	7	106	58
227	7	107	59
228	7	108	60
229	7	109	61
230	7	110	62
231	7	111	63
232	7	112	64
233	7	113	65
234	7	114	66
235	7	115	67
236	7	116	68
237	7	117	69
238	7	118	70
239	7	119	71
240	7	120	72
241	7	121	73
242	7	122	74
243	7	123	75
244	7	124	76
245	7	125	77
246	7	126	78
247	7	127	79
248	7	128	80
249	7	129	81
250	7	130	82
251	7	131	83
252	7	132	84
253	7	133	85
254	7	134	86
255	7	135	87
256	7	136	88
257	7	137	89
258	7	138	90
\.


--
-- Data for Name: tests; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tests (id, job_id, category_id, title, duration_minutes, passing_score, total_questions, is_random, created_at, updated_at, test_type, department_id, target_employee_type) FROM stdin;
2	1	2	Tes DISC personality	15	0.00	24	t	2026-08-26 20:57:41	2026-08-26 20:57:41	recruitment	\N	all
3	2	2	Tes DISC Iot	15	0.00	24	t	2026-08-26 21:31:11	2026-08-26 21:31:11	recruitment	\N	all
4	4	2	Tes DISC Personality	15	0.00	24	f	2026-08-27 10:06:18	2026-08-27 10:06:18	recruitment	\N	all
5	\N	2	DISC MAGANG 2026	30	0.00	24	f	2026-09-01 10:45:07	2026-09-03 08:37:26	employee	2	internship
6	\N	2	DISC KARYAWAN 2026	30	0.00	24	f	2026-09-02 09:50:14	2026-09-04 08:27:27	employee	4	permanent
1	3	2	Tes DISC	15	0.00	24	f	2026-08-26 14:02:42	2026-09-09 20:10:01	recruitment	\N	all
7	\N	3	Tes Kepribadian (PAPI Kostick)	60	0.00	90	f	2026-09-24 08:19:23	2026-09-24 08:19:23	employee	\N	all
\.


--
-- Data for Name: trainings; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.trainings (id, profile_id, name, certificate_path) FROM stdin;
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, role_id, nik, name, email, email_verified_at, password, remember_token, created_at, updated_at, google_id, avatar, is_recruiter) FROM stdin;
3	2	9999999999999999	Recruiter Team	recruiter@mail.com	\N	$2y$12$4fldBOhh4rr7Qbt5pEle7uZ2/mv3xdiNvooO8kd8bPI.9FhdxiUHG	\N	2026-08-26 10:42:46	2026-08-26 10:42:46	\N	\N	f
5	4	3322192965320002	Syauqi Maul	syauqi@gmail.com	\N	$2y$12$ooD7kDFwE9LU0VyaKop/X.JKiqn/08X0NOarnMpRA82Apjgpei6qO	\N	2026-09-01 10:43:10	2026-09-01 10:43:10	\N	\N	f
6	4	3374124506980002	DWI LESTARI INDAH SARI	dwilestariindahs56@gmail.com	\N	$2y$12$v8AdAtgmnnNrRnenftMR/uoL552Tk5Pskmy6hIJ8vTFi3FRA531Di	\N	2026-09-01 11:04:05	2026-09-01 11:04:05	\N	\N	f
1	1	0000000000000000	Administrator/HR	admin@mail.com	\N	$2y$12$sooM0zBw6lKaUq.2bUI8p.Ltlg820gDLNz5cu7v/Qg4nxPlbAaOmq	\N	2026-08-26 10:42:45	2026-09-17 10:00:30	\N	\N	f
2	4	3374000011112222	Ilham Taruprasetyo	ilham@gmail.com	\N	$2y$12$lwikldyyewNfs6ExgkYgwOQaYulNWAnSzPuIHgKrlbbwkK88kEk32	\N	2026-08-26 10:42:45	2026-09-01 11:37:45	\N	\N	f
9	4	3306084412040001	Dita Tri Handayani	ditatrihan@gmail.com	\N	$2y$12$AKOkxjrHUKarxya3NK/9PuYvkyxSWEluQ4LsOPX6z50.l15fyZM2u	sVdxxNhvy2BWBoTAxCAvelmfHuYlBuyL3GctRvAT5chbjXp7UBMjj87M4QCF	2026-09-01 11:43:00	2026-09-01 11:43:00	\N	\N	f
10	4	3175065210040016	Hasna Nur Aisyah Makarim	hasnanuraisyah523@gmail.com	\N	$2y$12$wF6Cna9YvZ.MKhSCTFPNi.yCoBd3dlPorDz8wCXrCylRXpoQej9ti	\N	2026-09-01 11:44:10	2026-09-01 11:44:10	\N	\N	f
11	4	3309067105980001	Galuh Prasetya Ningrum	galuhpningrum.gpn@gmail.com	\N	$2y$12$3OCvsmv1hV1aY7havvfCA.Adj0coWw4UNDpPcxx2tlEDi7JCD1mTe	\N	2026-09-02 10:31:43	2026-09-02 10:31:43	\N	\N	f
12	4	3374066207020002	Anggun Widiasari	anggunwidiasari2272@gmail.com	\N	$2y$12$BuyIyHHOL0Pu8kFUlaKvsuWkbxWT.nL.XuF0crk/LbIZkjcfm.NDq	\N	2026-09-02 10:31:59	2026-09-02 10:31:59	\N	\N	f
15	4	3327001234567890	Faishal Sitohang	faishal@gmail.com	\N	$2y$12$/rFMFXpjEktalowNx6MiEezRSbrhpFQwP0CE8V3q6yX7p3LPN03Vi	\N	2026-09-02 15:11:03	2026-09-03 08:39:32	\N	\N	f
14	4	3323071308980001	Samsul Hidayat	samsayat1@gmail.com	\N	$2y$12$jWe/BzVNky07Ny/3qS6GxuMzsbbqwDnTV3Y.46QDEuSjeiwbCSSTu	\N	2026-09-02 12:29:57	2026-09-03 08:52:55	\N	\N	f
4	3	9933220101090002	Mahargya Nashrullah	arul@gmail.com	\N	$2y$12$D94OqlZk.9lM9S0jmJgvVO/hhDBFthEx4h/2V9A/wGbICqrJ.Zk8C	\N	2026-09-01 10:35:52	2026-09-29 08:30:47	\N	applicant-photos/6abb14c786f30.jpg	f
17	3	1234567891234567	budi	budisantoso@gmail.com	\N	$2y$12$nxXZmICxu/7jytVH57M/rOWV829aHN9/ZfoMWDXq9OoffiFg.WOcW	\N	2026-09-07 13:54:31	2026-09-07 13:54:31	\N	\N	f
16	3	3322165656560001	Mail Maulana	myil290605@gmail.com	\N	\N	\N	2026-09-07 08:18:52	2026-09-17 09:53:20	110882809402974338912	https://lh3.googleusercontent.com/a/ACg8ocLm3HNUpffm1vVR3UIi8rQ7hi00e14e9JKLavlQQDPD7Qs0=s96-c	f
\.


--
-- Data for Name: work_experiences; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.work_experiences (id, profile_id, company_name, "position", employment_type, start_date, end_date, currently_working, description) FROM stdin;
1	1	Mitra Karya Analitika	Web Developer	Magang / Internship	2026-08-03	2026-12-31	f	\N
2	2	PT Mulia	Staff IT	Magang / Internship	2026-08-03	\N	t	\N
3	3	PT MIKA	WEB DEV	Magang / Internship	2026-08-03	\N	t	\N
\.


--
-- Name: achievements_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.achievements_id_seq', 1, false);


--
-- Name: applicant_families_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.applicant_families_id_seq', 3, true);


--
-- Name: applicant_job_preferences_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.applicant_job_preferences_id_seq', 1, true);


--
-- Name: applicant_profile_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.applicant_profile_id_seq', 4, true);


--
-- Name: application_status_history_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.application_status_history_id_seq', 23, true);


--
-- Name: certifications_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.certifications_id_seq', 1, false);


--
-- Name: companies_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.companies_id_seq', 5, true);


--
-- Name: company_showcases_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.company_showcases_id_seq', 3, true);


--
-- Name: degrees_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.degrees_id_seq', 5, true);


--
-- Name: department_test_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.department_test_id_seq', 5, true);


--
-- Name: departments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.departments_id_seq', 5, true);


--
-- Name: disc_norms_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.disc_norms_id_seq', 396, true);


--
-- Name: disc_profiles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.disc_profiles_id_seq', 1, false);


--
-- Name: disc_test_results_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.disc_test_results_id_seq', 10, true);


--
-- Name: disc_traits_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.disc_traits_id_seq', 4, true);


--
-- Name: educations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.educations_id_seq', 4, true);


--
-- Name: employee_profiles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.employee_profiles_id_seq', 12, true);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: interview_schedule_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.interview_schedule_id_seq', 1, false);


--
-- Name: job_applications_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.job_applications_id_seq', 8, true);


--
-- Name: job_degrees_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.job_degrees_id_seq', 1, false);


--
-- Name: job_majors_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.job_majors_id_seq', 1, false);


--
-- Name: job_test_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.job_test_id_seq', 4, true);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.jobs_id_seq', 5, true);


--
-- Name: languages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.languages_id_seq', 1, false);


--
-- Name: majors_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.majors_id_seq', 6, true);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 33, true);


--
-- Name: organizations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.organizations_id_seq', 1, false);


--
-- Name: papi_aspects_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.papi_aspects_id_seq', 7, true);


--
-- Name: papi_factors_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.papi_factors_id_seq', 20, true);


--
-- Name: papi_norms_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.papi_norms_id_seq', 60, true);


--
-- Name: papi_test_results_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.papi_test_results_id_seq', 2, true);


--
-- Name: positions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.positions_id_seq', 10, true);


--
-- Name: question_banks_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.question_banks_id_seq', 138, true);


--
-- Name: question_options_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.question_options_id_seq', 372, true);


--
-- Name: queue_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.queue_jobs_id_seq', 1, false);


--
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.roles_id_seq', 1, false);


--
-- Name: skills_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.skills_id_seq', 3, true);


--
-- Name: social_medias_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.social_medias_id_seq', 1, false);


--
-- Name: test_answers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.test_answers_id_seq', 996, true);


--
-- Name: test_attempts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.test_attempts_id_seq', 19, true);


--
-- Name: test_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.test_categories_id_seq', 3, true);


--
-- Name: test_questions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.test_questions_id_seq', 258, true);


--
-- Name: tests_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tests_id_seq', 7, true);


--
-- Name: trainings_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.trainings_id_seq', 1, false);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 17, true);


--
-- Name: work_experiences_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.work_experiences_id_seq', 3, true);


--
-- Name: achievements achievements_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.achievements
    ADD CONSTRAINT achievements_pkey PRIMARY KEY (id);


--
-- Name: applicant_families applicant_families_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.applicant_families
    ADD CONSTRAINT applicant_families_pkey PRIMARY KEY (id);


--
-- Name: applicant_job_preferences applicant_job_preferences_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.applicant_job_preferences
    ADD CONSTRAINT applicant_job_preferences_pkey PRIMARY KEY (id);


--
-- Name: applicant_profile applicant_profile_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.applicant_profile
    ADD CONSTRAINT applicant_profile_pkey PRIMARY KEY (id);


--
-- Name: applicant_profile applicant_profile_user_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.applicant_profile
    ADD CONSTRAINT applicant_profile_user_id_unique UNIQUE (user_id);


--
-- Name: application_status_history application_status_history_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.application_status_history
    ADD CONSTRAINT application_status_history_pkey PRIMARY KEY (id);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: certifications certifications_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.certifications
    ADD CONSTRAINT certifications_pkey PRIMARY KEY (id);


--
-- Name: companies companies_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.companies
    ADD CONSTRAINT companies_pkey PRIMARY KEY (id);


--
-- Name: company_showcases company_showcases_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.company_showcases
    ADD CONSTRAINT company_showcases_pkey PRIMARY KEY (id);


--
-- Name: degrees degrees_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.degrees
    ADD CONSTRAINT degrees_pkey PRIMARY KEY (id);


--
-- Name: department_test department_test_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.department_test
    ADD CONSTRAINT department_test_pkey PRIMARY KEY (id);


--
-- Name: department_test department_test_test_id_department_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.department_test
    ADD CONSTRAINT department_test_test_id_department_id_unique UNIQUE (test_id, department_id);


--
-- Name: departments departments_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.departments
    ADD CONSTRAINT departments_pkey PRIMARY KEY (id);


--
-- Name: disc_norms disc_norms_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.disc_norms
    ADD CONSTRAINT disc_norms_pkey PRIMARY KEY (id);


--
-- Name: disc_profiles disc_profiles_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.disc_profiles
    ADD CONSTRAINT disc_profiles_pkey PRIMARY KEY (id);


--
-- Name: disc_test_results disc_test_results_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.disc_test_results
    ADD CONSTRAINT disc_test_results_pkey PRIMARY KEY (id);


--
-- Name: disc_traits disc_traits_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.disc_traits
    ADD CONSTRAINT disc_traits_pkey PRIMARY KEY (id);


--
-- Name: educations educations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.educations
    ADD CONSTRAINT educations_pkey PRIMARY KEY (id);


--
-- Name: employee_profiles employee_profiles_nik_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.employee_profiles
    ADD CONSTRAINT employee_profiles_nik_unique UNIQUE (nik);


--
-- Name: employee_profiles employee_profiles_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.employee_profiles
    ADD CONSTRAINT employee_profiles_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: interview_schedule interview_schedule_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.interview_schedule
    ADD CONSTRAINT interview_schedule_pkey PRIMARY KEY (id);


--
-- Name: job_applications job_applications_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_applications
    ADD CONSTRAINT job_applications_pkey PRIMARY KEY (id);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: job_degrees job_degrees_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_degrees
    ADD CONSTRAINT job_degrees_pkey PRIMARY KEY (id);


--
-- Name: job_majors job_majors_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_majors
    ADD CONSTRAINT job_majors_pkey PRIMARY KEY (id);


--
-- Name: job_test job_test_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_test
    ADD CONSTRAINT job_test_pkey PRIMARY KEY (id);


--
-- Name: job_test job_test_test_id_job_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_test
    ADD CONSTRAINT job_test_test_id_job_id_unique UNIQUE (test_id, job_id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: languages languages_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.languages
    ADD CONSTRAINT languages_pkey PRIMARY KEY (id);


--
-- Name: majors majors_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.majors
    ADD CONSTRAINT majors_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: organizations organizations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.organizations
    ADD CONSTRAINT organizations_pkey PRIMARY KEY (id);


--
-- Name: papi_aspects papi_aspects_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.papi_aspects
    ADD CONSTRAINT papi_aspects_pkey PRIMARY KEY (id);


--
-- Name: papi_factors papi_factors_code_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.papi_factors
    ADD CONSTRAINT papi_factors_code_unique UNIQUE (code);


--
-- Name: papi_factors papi_factors_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.papi_factors
    ADD CONSTRAINT papi_factors_pkey PRIMARY KEY (id);


--
-- Name: papi_norms papi_norms_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.papi_norms
    ADD CONSTRAINT papi_norms_pkey PRIMARY KEY (id);


--
-- Name: papi_test_results papi_test_results_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.papi_test_results
    ADD CONSTRAINT papi_test_results_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: positions positions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.positions
    ADD CONSTRAINT positions_pkey PRIMARY KEY (id);


--
-- Name: question_banks question_banks_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.question_banks
    ADD CONSTRAINT question_banks_pkey PRIMARY KEY (id);


--
-- Name: question_options question_options_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.question_options
    ADD CONSTRAINT question_options_pkey PRIMARY KEY (id);


--
-- Name: queue_jobs queue_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.queue_jobs
    ADD CONSTRAINT queue_jobs_pkey PRIMARY KEY (id);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: skills skills_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.skills
    ADD CONSTRAINT skills_pkey PRIMARY KEY (id);


--
-- Name: social_medias social_medias_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.social_medias
    ADD CONSTRAINT social_medias_pkey PRIMARY KEY (id);


--
-- Name: test_answers test_answers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.test_answers
    ADD CONSTRAINT test_answers_pkey PRIMARY KEY (id);


--
-- Name: test_attempts test_attempts_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.test_attempts
    ADD CONSTRAINT test_attempts_pkey PRIMARY KEY (id);


--
-- Name: test_categories test_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.test_categories
    ADD CONSTRAINT test_categories_pkey PRIMARY KEY (id);


--
-- Name: test_questions test_questions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.test_questions
    ADD CONSTRAINT test_questions_pkey PRIMARY KEY (id);


--
-- Name: tests tests_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tests
    ADD CONSTRAINT tests_pkey PRIMARY KEY (id);


--
-- Name: trainings trainings_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.trainings
    ADD CONSTRAINT trainings_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_google_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_google_id_unique UNIQUE (google_id);


--
-- Name: users users_nik_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_nik_unique UNIQUE (nik);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: work_experiences work_experiences_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.work_experiences
    ADD CONSTRAINT work_experiences_pkey PRIMARY KEY (id);


--
-- Name: cache_expiration_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX cache_expiration_index ON public.cache USING btree (expiration);


--
-- Name: cache_locks_expiration_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX cache_locks_expiration_index ON public.cache_locks USING btree (expiration);


--
-- Name: failed_jobs_connection_queue_failed_at_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX failed_jobs_connection_queue_failed_at_index ON public.failed_jobs USING btree (connection, queue, failed_at);


--
-- Name: papi_norms_factor_code_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX papi_norms_factor_code_index ON public.papi_norms USING btree (factor_code);


--
-- Name: queue_jobs_queue_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX queue_jobs_queue_index ON public.queue_jobs USING btree (queue);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: achievements achievements_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.achievements
    ADD CONSTRAINT achievements_profile_id_foreign FOREIGN KEY (profile_id) REFERENCES public.applicant_profile(id) ON DELETE CASCADE;


--
-- Name: applicant_families applicant_families_applicant_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.applicant_families
    ADD CONSTRAINT applicant_families_applicant_profile_id_foreign FOREIGN KEY (applicant_profile_id) REFERENCES public.applicant_profile(id) ON DELETE CASCADE;


--
-- Name: applicant_job_preferences applicant_job_preferences_applicant_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.applicant_job_preferences
    ADD CONSTRAINT applicant_job_preferences_applicant_profile_id_foreign FOREIGN KEY (applicant_profile_id) REFERENCES public.applicant_profile(id) ON DELETE CASCADE;


--
-- Name: applicant_profile applicant_profile_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.applicant_profile
    ADD CONSTRAINT applicant_profile_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: application_status_history application_status_history_changed_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.application_status_history
    ADD CONSTRAINT application_status_history_changed_by_foreign FOREIGN KEY (changed_by) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: application_status_history application_status_history_job_applications_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.application_status_history
    ADD CONSTRAINT application_status_history_job_applications_id_foreign FOREIGN KEY (job_applications_id) REFERENCES public.job_applications(id) ON DELETE CASCADE;


--
-- Name: certifications certifications_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.certifications
    ADD CONSTRAINT certifications_profile_id_foreign FOREIGN KEY (profile_id) REFERENCES public.applicant_profile(id) ON DELETE CASCADE;


--
-- Name: companies companies_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.companies
    ADD CONSTRAINT companies_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE SET NULL;


--
-- Name: company_showcases company_showcases_company_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.company_showcases
    ADD CONSTRAINT company_showcases_company_id_foreign FOREIGN KEY (company_id) REFERENCES public.companies(id) ON DELETE SET NULL;


--
-- Name: department_test department_test_department_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.department_test
    ADD CONSTRAINT department_test_department_id_foreign FOREIGN KEY (department_id) REFERENCES public.departments(id) ON DELETE CASCADE;


--
-- Name: department_test department_test_test_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.department_test
    ADD CONSTRAINT department_test_test_id_foreign FOREIGN KEY (test_id) REFERENCES public.tests(id) ON DELETE CASCADE;


--
-- Name: departments departments_company_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.departments
    ADD CONSTRAINT departments_company_id_foreign FOREIGN KEY (company_id) REFERENCES public.companies(id) ON DELETE CASCADE;


--
-- Name: disc_test_results disc_test_results_disc_profiles_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.disc_test_results
    ADD CONSTRAINT disc_test_results_disc_profiles_id_foreign FOREIGN KEY (disc_profiles_id) REFERENCES public.disc_profiles(id) ON DELETE SET NULL;


--
-- Name: disc_test_results disc_test_results_test_attempt_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.disc_test_results
    ADD CONSTRAINT disc_test_results_test_attempt_id_foreign FOREIGN KEY (test_attempt_id) REFERENCES public.test_attempts(id) ON DELETE CASCADE;


--
-- Name: educations educations_degree_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.educations
    ADD CONSTRAINT educations_degree_id_foreign FOREIGN KEY (degree_id) REFERENCES public.degrees(id) ON DELETE SET NULL;


--
-- Name: educations educations_major_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.educations
    ADD CONSTRAINT educations_major_id_foreign FOREIGN KEY (major_id) REFERENCES public.majors(id) ON DELETE SET NULL;


--
-- Name: educations educations_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.educations
    ADD CONSTRAINT educations_profile_id_foreign FOREIGN KEY (profile_id) REFERENCES public.applicant_profile(id) ON DELETE CASCADE;


--
-- Name: employee_profiles employee_profiles_company_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.employee_profiles
    ADD CONSTRAINT employee_profiles_company_id_foreign FOREIGN KEY (company_id) REFERENCES public.companies(id) ON DELETE SET NULL;


--
-- Name: employee_profiles employee_profiles_department_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.employee_profiles
    ADD CONSTRAINT employee_profiles_department_id_foreign FOREIGN KEY (department_id) REFERENCES public.departments(id) ON DELETE SET NULL;


--
-- Name: employee_profiles employee_profiles_position_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.employee_profiles
    ADD CONSTRAINT employee_profiles_position_id_foreign FOREIGN KEY (position_id) REFERENCES public.positions(id) ON DELETE SET NULL;


--
-- Name: employee_profiles employee_profiles_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.employee_profiles
    ADD CONSTRAINT employee_profiles_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: interview_schedule interview_schedule_job_applications_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.interview_schedule
    ADD CONSTRAINT interview_schedule_job_applications_id_foreign FOREIGN KEY (job_applications_id) REFERENCES public.job_applications(id) ON DELETE CASCADE;


--
-- Name: interview_schedule interview_schedule_users_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.interview_schedule
    ADD CONSTRAINT interview_schedule_users_id_foreign FOREIGN KEY (users_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: job_applications job_applications_admin_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_applications
    ADD CONSTRAINT job_applications_admin_id_foreign FOREIGN KEY (admin_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: job_applications job_applications_job_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_applications
    ADD CONSTRAINT job_applications_job_id_foreign FOREIGN KEY (job_id) REFERENCES public.jobs(id) ON DELETE CASCADE;


--
-- Name: job_applications job_applications_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_applications
    ADD CONSTRAINT job_applications_profile_id_foreign FOREIGN KEY (profile_id) REFERENCES public.applicant_profile(id) ON DELETE CASCADE;


--
-- Name: job_applications job_applications_recruiter_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_applications
    ADD CONSTRAINT job_applications_recruiter_id_foreign FOREIGN KEY (recruiter_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: job_degrees job_degrees_degree_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_degrees
    ADD CONSTRAINT job_degrees_degree_id_foreign FOREIGN KEY (degree_id) REFERENCES public.degrees(id) ON DELETE CASCADE;


--
-- Name: job_degrees job_degrees_job_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_degrees
    ADD CONSTRAINT job_degrees_job_id_foreign FOREIGN KEY (job_id) REFERENCES public.jobs(id) ON DELETE CASCADE;


--
-- Name: job_majors job_majors_job_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_majors
    ADD CONSTRAINT job_majors_job_id_foreign FOREIGN KEY (job_id) REFERENCES public.jobs(id) ON DELETE CASCADE;


--
-- Name: job_majors job_majors_major_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_majors
    ADD CONSTRAINT job_majors_major_id_foreign FOREIGN KEY (major_id) REFERENCES public.majors(id) ON DELETE CASCADE;


--
-- Name: job_test job_test_job_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_test
    ADD CONSTRAINT job_test_job_id_foreign FOREIGN KEY (job_id) REFERENCES public.jobs(id) ON DELETE CASCADE;


--
-- Name: job_test job_test_test_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_test
    ADD CONSTRAINT job_test_test_id_foreign FOREIGN KEY (test_id) REFERENCES public.tests(id) ON DELETE CASCADE;


--
-- Name: jobs jobs_company_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_company_id_foreign FOREIGN KEY (company_id) REFERENCES public.companies(id) ON DELETE CASCADE;


--
-- Name: jobs jobs_department_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_department_id_foreign FOREIGN KEY (department_id) REFERENCES public.departments(id) ON DELETE CASCADE;


--
-- Name: jobs jobs_position_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_position_id_foreign FOREIGN KEY (position_id) REFERENCES public.positions(id) ON DELETE SET NULL;


--
-- Name: jobs jobs_reviewer_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_reviewer_id_foreign FOREIGN KEY (reviewer_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: languages languages_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.languages
    ADD CONSTRAINT languages_profile_id_foreign FOREIGN KEY (profile_id) REFERENCES public.applicant_profile(id) ON DELETE CASCADE;


--
-- Name: organizations organizations_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.organizations
    ADD CONSTRAINT organizations_profile_id_foreign FOREIGN KEY (profile_id) REFERENCES public.applicant_profile(id) ON DELETE CASCADE;


--
-- Name: papi_factors papi_factors_aspect_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.papi_factors
    ADD CONSTRAINT papi_factors_aspect_id_foreign FOREIGN KEY (aspect_id) REFERENCES public.papi_aspects(id) ON DELETE CASCADE;


--
-- Name: papi_norms papi_norms_factor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.papi_norms
    ADD CONSTRAINT papi_norms_factor_id_foreign FOREIGN KEY (factor_id) REFERENCES public.papi_factors(id) ON DELETE CASCADE;


--
-- Name: papi_test_results papi_test_results_test_attempt_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.papi_test_results
    ADD CONSTRAINT papi_test_results_test_attempt_id_foreign FOREIGN KEY (test_attempt_id) REFERENCES public.test_attempts(id) ON DELETE CASCADE;


--
-- Name: positions positions_department_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.positions
    ADD CONSTRAINT positions_department_id_foreign FOREIGN KEY (department_id) REFERENCES public.departments(id) ON DELETE CASCADE;


--
-- Name: question_banks question_banks_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.question_banks
    ADD CONSTRAINT question_banks_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.test_categories(id) ON DELETE CASCADE;


--
-- Name: question_options question_options_question_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.question_options
    ADD CONSTRAINT question_options_question_id_foreign FOREIGN KEY (question_id) REFERENCES public.question_banks(id) ON DELETE CASCADE;


--
-- Name: skills skills_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.skills
    ADD CONSTRAINT skills_profile_id_foreign FOREIGN KEY (profile_id) REFERENCES public.applicant_profile(id) ON DELETE CASCADE;


--
-- Name: social_medias social_medias_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.social_medias
    ADD CONSTRAINT social_medias_profile_id_foreign FOREIGN KEY (profile_id) REFERENCES public.applicant_profile(id) ON DELETE CASCADE;


--
-- Name: test_answers test_answers_attempt_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.test_answers
    ADD CONSTRAINT test_answers_attempt_id_foreign FOREIGN KEY (attempt_id) REFERENCES public.test_attempts(id) ON DELETE CASCADE;


--
-- Name: test_answers test_answers_option_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.test_answers
    ADD CONSTRAINT test_answers_option_id_foreign FOREIGN KEY (option_id) REFERENCES public.question_options(id) ON DELETE SET NULL;


--
-- Name: test_answers test_answers_question_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.test_answers
    ADD CONSTRAINT test_answers_question_id_foreign FOREIGN KEY (question_id) REFERENCES public.question_banks(id) ON DELETE CASCADE;


--
-- Name: test_answers test_answers_reviewed_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.test_answers
    ADD CONSTRAINT test_answers_reviewed_by_foreign FOREIGN KEY (reviewed_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: test_attempts test_attempts_job_application_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.test_attempts
    ADD CONSTRAINT test_attempts_job_application_id_foreign FOREIGN KEY (job_application_id) REFERENCES public.job_applications(id) ON DELETE CASCADE;


--
-- Name: test_attempts test_attempts_test_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.test_attempts
    ADD CONSTRAINT test_attempts_test_id_foreign FOREIGN KEY (test_id) REFERENCES public.tests(id) ON DELETE CASCADE;


--
-- Name: test_attempts test_attempts_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.test_attempts
    ADD CONSTRAINT test_attempts_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: test_questions test_questions_question_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.test_questions
    ADD CONSTRAINT test_questions_question_id_foreign FOREIGN KEY (question_id) REFERENCES public.question_banks(id) ON DELETE CASCADE;


--
-- Name: test_questions test_questions_test_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.test_questions
    ADD CONSTRAINT test_questions_test_id_foreign FOREIGN KEY (test_id) REFERENCES public.tests(id) ON DELETE CASCADE;


--
-- Name: tests tests_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tests
    ADD CONSTRAINT tests_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.test_categories(id) ON DELETE CASCADE;


--
-- Name: tests tests_department_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tests
    ADD CONSTRAINT tests_department_id_foreign FOREIGN KEY (department_id) REFERENCES public.departments(id) ON DELETE SET NULL;


--
-- Name: tests tests_job_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tests
    ADD CONSTRAINT tests_job_id_foreign FOREIGN KEY (job_id) REFERENCES public.jobs(id) ON DELETE CASCADE;


--
-- Name: trainings trainings_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.trainings
    ADD CONSTRAINT trainings_profile_id_foreign FOREIGN KEY (profile_id) REFERENCES public.applicant_profile(id) ON DELETE CASCADE;


--
-- Name: users users_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: work_experiences work_experiences_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.work_experiences
    ADD CONSTRAINT work_experiences_profile_id_foreign FOREIGN KEY (profile_id) REFERENCES public.applicant_profile(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict ycKleqDS5k8QLeneFSKgGBddibW2F1bf6k231an58VQn4qSadReuotuNgi2GHbp

