--
-- PostgreSQL database dump
--

\restrict WXeOeT3LWrqCXWLoOB9AsC6qR0sKKtsneEc7gApjyxi0uFiAtP5750Dp0nBC0X4

-- Dumped from database version 17.6
-- Dumped by pg_dump version 17.11 (Ubuntu 17.11-1.pgdg24.04+2)

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

--
-- Name: auth; Type: SCHEMA; Schema: -; Owner: supabase_admin
--

CREATE SCHEMA auth;


ALTER SCHEMA auth OWNER TO supabase_admin;

--
-- Name: extensions; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA extensions;


ALTER SCHEMA extensions OWNER TO postgres;

--
-- Name: graphql; Type: SCHEMA; Schema: -; Owner: supabase_admin
--

CREATE SCHEMA graphql;


ALTER SCHEMA graphql OWNER TO supabase_admin;

--
-- Name: graphql_public; Type: SCHEMA; Schema: -; Owner: supabase_admin
--

CREATE SCHEMA graphql_public;


ALTER SCHEMA graphql_public OWNER TO supabase_admin;

--
-- Name: pgbouncer; Type: SCHEMA; Schema: -; Owner: pgbouncer
--

CREATE SCHEMA pgbouncer;


ALTER SCHEMA pgbouncer OWNER TO pgbouncer;

--
-- Name: realtime; Type: SCHEMA; Schema: -; Owner: supabase_admin
--

CREATE SCHEMA realtime;


ALTER SCHEMA realtime OWNER TO supabase_admin;

--
-- Name: storage; Type: SCHEMA; Schema: -; Owner: supabase_admin
--

CREATE SCHEMA storage;


ALTER SCHEMA storage OWNER TO supabase_admin;

--
-- Name: vault; Type: SCHEMA; Schema: -; Owner: supabase_admin
--

CREATE SCHEMA vault;


ALTER SCHEMA vault OWNER TO supabase_admin;

--
-- Name: pg_stat_statements; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS pg_stat_statements WITH SCHEMA extensions;


--
-- Name: EXTENSION pg_stat_statements; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION pg_stat_statements IS 'track planning and execution statistics of all SQL statements executed';


--
-- Name: pgcrypto; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS pgcrypto WITH SCHEMA extensions;


--
-- Name: EXTENSION pgcrypto; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION pgcrypto IS 'cryptographic functions';


--
-- Name: supabase_vault; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS supabase_vault WITH SCHEMA vault;


--
-- Name: EXTENSION supabase_vault; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION supabase_vault IS 'Supabase Vault Extension';


--
-- Name: uuid-ossp; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS "uuid-ossp" WITH SCHEMA extensions;


--
-- Name: EXTENSION "uuid-ossp"; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION "uuid-ossp" IS 'generate universally unique identifiers (UUIDs)';


--
-- Name: aal_level; Type: TYPE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TYPE auth.aal_level AS ENUM (
    'aal1',
    'aal2',
    'aal3'
);


ALTER TYPE auth.aal_level OWNER TO supabase_auth_admin;

--
-- Name: code_challenge_method; Type: TYPE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TYPE auth.code_challenge_method AS ENUM (
    's256',
    'plain'
);


ALTER TYPE auth.code_challenge_method OWNER TO supabase_auth_admin;

--
-- Name: factor_status; Type: TYPE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TYPE auth.factor_status AS ENUM (
    'unverified',
    'verified'
);


ALTER TYPE auth.factor_status OWNER TO supabase_auth_admin;

--
-- Name: factor_type; Type: TYPE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TYPE auth.factor_type AS ENUM (
    'totp',
    'webauthn',
    'phone',
    'recovery_code'
);


ALTER TYPE auth.factor_type OWNER TO supabase_auth_admin;

--
-- Name: oauth_authorization_status; Type: TYPE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TYPE auth.oauth_authorization_status AS ENUM (
    'pending',
    'approved',
    'denied',
    'expired'
);


ALTER TYPE auth.oauth_authorization_status OWNER TO supabase_auth_admin;

--
-- Name: oauth_client_type; Type: TYPE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TYPE auth.oauth_client_type AS ENUM (
    'public',
    'confidential'
);


ALTER TYPE auth.oauth_client_type OWNER TO supabase_auth_admin;

--
-- Name: oauth_registration_type; Type: TYPE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TYPE auth.oauth_registration_type AS ENUM (
    'dynamic',
    'manual'
);


ALTER TYPE auth.oauth_registration_type OWNER TO supabase_auth_admin;

--
-- Name: oauth_response_type; Type: TYPE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TYPE auth.oauth_response_type AS ENUM (
    'code'
);


ALTER TYPE auth.oauth_response_type OWNER TO supabase_auth_admin;

--
-- Name: one_time_token_type; Type: TYPE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TYPE auth.one_time_token_type AS ENUM (
    'confirmation_token',
    'reauthentication_token',
    'recovery_token',
    'email_change_token_new',
    'email_change_token_current',
    'phone_change_token'
);


ALTER TYPE auth.one_time_token_type OWNER TO supabase_auth_admin;

--
-- Name: action; Type: TYPE; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE TYPE realtime.action AS ENUM (
    'INSERT',
    'UPDATE',
    'DELETE',
    'TRUNCATE',
    'ERROR'
);


ALTER TYPE realtime.action OWNER TO supabase_realtime_admin;

--
-- Name: equality_op; Type: TYPE; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE TYPE realtime.equality_op AS ENUM (
    'eq',
    'neq',
    'lt',
    'lte',
    'gt',
    'gte',
    'in',
    'like',
    'ilike',
    'is',
    'match',
    'imatch',
    'isdistinct'
);


ALTER TYPE realtime.equality_op OWNER TO supabase_realtime_admin;

--
-- Name: user_defined_filter; Type: TYPE; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE TYPE realtime.user_defined_filter AS (
	column_name text,
	op realtime.equality_op,
	value text,
	negate boolean
);


ALTER TYPE realtime.user_defined_filter OWNER TO supabase_realtime_admin;

--
-- Name: wal_column; Type: TYPE; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE TYPE realtime.wal_column AS (
	name text,
	type_name text,
	type_oid oid,
	value jsonb,
	is_pkey boolean,
	is_selectable boolean
);


ALTER TYPE realtime.wal_column OWNER TO supabase_realtime_admin;

--
-- Name: wal_rls; Type: TYPE; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE TYPE realtime.wal_rls AS (
	wal jsonb,
	is_rls_enabled boolean,
	subscription_ids uuid[],
	errors text[]
);


ALTER TYPE realtime.wal_rls OWNER TO supabase_realtime_admin;

--
-- Name: buckettype; Type: TYPE; Schema: storage; Owner: supabase_storage_admin
--

CREATE TYPE storage.buckettype AS ENUM (
    'STANDARD',
    'ANALYTICS',
    'VECTOR'
);


ALTER TYPE storage.buckettype OWNER TO supabase_storage_admin;

--
-- Name: email(); Type: FUNCTION; Schema: auth; Owner: supabase_auth_admin
--

CREATE FUNCTION auth.email() RETURNS text
    LANGUAGE sql STABLE
    AS $$
  select 
  coalesce(
    nullif(current_setting('request.jwt.claim.email', true), ''),
    (nullif(current_setting('request.jwt.claims', true), '')::jsonb ->> 'email')
  )::text
$$;


ALTER FUNCTION auth.email() OWNER TO supabase_auth_admin;

--
-- Name: FUNCTION email(); Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON FUNCTION auth.email() IS 'Deprecated. Use auth.jwt() -> ''email'' instead.';


--
-- Name: jwt(); Type: FUNCTION; Schema: auth; Owner: supabase_auth_admin
--

CREATE FUNCTION auth.jwt() RETURNS jsonb
    LANGUAGE sql STABLE
    AS $$
  select 
    coalesce(
        nullif(current_setting('request.jwt.claim', true), ''),
        nullif(current_setting('request.jwt.claims', true), '')
    )::jsonb
$$;


ALTER FUNCTION auth.jwt() OWNER TO supabase_auth_admin;

--
-- Name: role(); Type: FUNCTION; Schema: auth; Owner: supabase_auth_admin
--

CREATE FUNCTION auth.role() RETURNS text
    LANGUAGE sql STABLE
    AS $$
  select 
  coalesce(
    nullif(current_setting('request.jwt.claim.role', true), ''),
    (nullif(current_setting('request.jwt.claims', true), '')::jsonb ->> 'role')
  )::text
$$;


ALTER FUNCTION auth.role() OWNER TO supabase_auth_admin;

--
-- Name: FUNCTION role(); Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON FUNCTION auth.role() IS 'Deprecated. Use auth.jwt() -> ''role'' instead.';


--
-- Name: uid(); Type: FUNCTION; Schema: auth; Owner: supabase_auth_admin
--

CREATE FUNCTION auth.uid() RETURNS uuid
    LANGUAGE sql STABLE
    AS $$
  select 
  coalesce(
    nullif(current_setting('request.jwt.claim.sub', true), ''),
    (nullif(current_setting('request.jwt.claims', true), '')::jsonb ->> 'sub')
  )::uuid
$$;


ALTER FUNCTION auth.uid() OWNER TO supabase_auth_admin;

--
-- Name: FUNCTION uid(); Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON FUNCTION auth.uid() IS 'Deprecated. Use auth.jwt() -> ''sub'' instead.';


--
-- Name: grant_pg_cron_access(); Type: FUNCTION; Schema: extensions; Owner: supabase_admin
--

CREATE FUNCTION extensions.grant_pg_cron_access() RETURNS event_trigger
    LANGUAGE plpgsql
    SET search_path TO ''
    AS $$
BEGIN
  IF EXISTS (
    SELECT
    FROM pg_event_trigger_ddl_commands() AS ev
    JOIN pg_extension AS ext
    ON ev.objid = ext.oid
    WHERE ext.extname = 'pg_cron'
  )
  THEN
    grant usage on schema cron to postgres with grant option;

    alter default privileges in schema cron grant all on tables to postgres with grant option;
    alter default privileges in schema cron grant all on functions to postgres with grant option;
    alter default privileges in schema cron grant all on sequences to postgres with grant option;

    alter default privileges for user supabase_admin in schema cron grant all
        on sequences to postgres with grant option;
    alter default privileges for user supabase_admin in schema cron grant all
        on tables to postgres with grant option;
    alter default privileges for user supabase_admin in schema cron grant all
        on functions to postgres with grant option;

    grant all privileges on all tables in schema cron to postgres with grant option;
    revoke all on table cron.job from postgres;
    grant select on table cron.job to postgres with grant option;
    revoke trigger on cron.job_run_details from postgres;
  END IF;
END;
$$;


ALTER FUNCTION extensions.grant_pg_cron_access() OWNER TO supabase_admin;

--
-- Name: FUNCTION grant_pg_cron_access(); Type: COMMENT; Schema: extensions; Owner: supabase_admin
--

COMMENT ON FUNCTION extensions.grant_pg_cron_access() IS 'Grants access to pg_cron';


--
-- Name: grant_pg_graphql_access(); Type: FUNCTION; Schema: extensions; Owner: supabase_admin
--

CREATE FUNCTION extensions.grant_pg_graphql_access() RETURNS event_trigger
    LANGUAGE plpgsql
    SET search_path TO ''
    AS $_$
begin
    if not exists (
        select 1
        from pg_catalog.pg_event_trigger_ddl_commands() ev
        join pg_catalog.pg_extension e on ev.objid = e.oid
        where e.extname = 'pg_graphql'
    ) then
        return;
    end if;

    drop function if exists graphql_public.graphql;
    create or replace function graphql_public.graphql(
        "operationName" text default null,
        query text default null,
        variables jsonb default null,
        extensions jsonb default null
    )
        returns jsonb
        language sql
    as $$
        select graphql.resolve(
            query := query,
            variables := coalesce(variables, '{}'),
            "operationName" := "operationName",
            extensions := extensions
        );
    $$;

    -- Attach the wrapper to the extension so DROP EXTENSION cascades to it,
    -- which in turn triggers set_graphql_placeholder to reinstall the "not enabled" stub.
    alter extension pg_graphql add function graphql_public.graphql(text, text, jsonb, jsonb);

    grant usage on schema graphql to postgres, anon, authenticated, service_role;
    grant execute on function graphql.resolve to postgres, anon, authenticated, service_role;
    grant usage on schema graphql to postgres with grant option;
    grant usage on schema graphql_public to postgres with grant option;
end;
$_$;


ALTER FUNCTION extensions.grant_pg_graphql_access() OWNER TO supabase_admin;

--
-- Name: FUNCTION grant_pg_graphql_access(); Type: COMMENT; Schema: extensions; Owner: supabase_admin
--

COMMENT ON FUNCTION extensions.grant_pg_graphql_access() IS 'Grants access to pg_graphql';


--
-- Name: grant_pg_net_access(); Type: FUNCTION; Schema: extensions; Owner: supabase_admin
--

CREATE FUNCTION extensions.grant_pg_net_access() RETURNS event_trigger
    LANGUAGE plpgsql
    SET search_path TO ''
    AS $$
BEGIN
  IF EXISTS (
    SELECT 1
    FROM pg_event_trigger_ddl_commands() AS ev
    JOIN pg_extension AS ext
    ON ev.objid = ext.oid
    WHERE ext.extname = 'pg_net'
  )
  THEN
    IF NOT EXISTS (
      SELECT 1
      FROM pg_roles
      WHERE rolname = 'supabase_functions_admin'
    )
    THEN
      CREATE USER supabase_functions_admin NOINHERIT CREATEROLE LOGIN NOREPLICATION;
    END IF;

    GRANT USAGE ON SCHEMA net TO supabase_functions_admin, postgres, anon, authenticated, service_role;

    IF EXISTS (
      SELECT FROM pg_extension
      WHERE extname = 'pg_net'
      -- all versions in use on existing projects as of 2025-02-20
      -- version 0.12.0 onwards don't need these applied
      AND extversion IN ('0.2', '0.6', '0.7', '0.7.1', '0.8.0', '0.10.0', '0.11.0')
    ) THEN
      ALTER function net.http_get(url text, params jsonb, headers jsonb, timeout_milliseconds integer) SECURITY DEFINER;
      ALTER function net.http_post(url text, body jsonb, params jsonb, headers jsonb, timeout_milliseconds integer) SECURITY DEFINER;

      ALTER function net.http_get(url text, params jsonb, headers jsonb, timeout_milliseconds integer) SET search_path = net;
      ALTER function net.http_post(url text, body jsonb, params jsonb, headers jsonb, timeout_milliseconds integer) SET search_path = net;

      REVOKE ALL ON FUNCTION net.http_get(url text, params jsonb, headers jsonb, timeout_milliseconds integer) FROM PUBLIC;
      REVOKE ALL ON FUNCTION net.http_post(url text, body jsonb, params jsonb, headers jsonb, timeout_milliseconds integer) FROM PUBLIC;

      GRANT EXECUTE ON FUNCTION net.http_get(url text, params jsonb, headers jsonb, timeout_milliseconds integer) TO supabase_functions_admin, postgres, anon, authenticated, service_role;
      GRANT EXECUTE ON FUNCTION net.http_post(url text, body jsonb, params jsonb, headers jsonb, timeout_milliseconds integer) TO supabase_functions_admin, postgres, anon, authenticated, service_role;
    END IF;
  END IF;
END;
$$;


ALTER FUNCTION extensions.grant_pg_net_access() OWNER TO supabase_admin;

--
-- Name: FUNCTION grant_pg_net_access(); Type: COMMENT; Schema: extensions; Owner: supabase_admin
--

COMMENT ON FUNCTION extensions.grant_pg_net_access() IS 'Grants access to pg_net';


--
-- Name: pgrst_ddl_watch(); Type: FUNCTION; Schema: extensions; Owner: supabase_admin
--

CREATE FUNCTION extensions.pgrst_ddl_watch() RETURNS event_trigger
    LANGUAGE plpgsql
    SET search_path TO ''
    AS $$
DECLARE
  cmd record;
BEGIN
  FOR cmd IN SELECT * FROM pg_event_trigger_ddl_commands()
  LOOP
    IF cmd.command_tag IN (
      'CREATE SCHEMA', 'ALTER SCHEMA'
    , 'CREATE TABLE', 'CREATE TABLE AS', 'SELECT INTO', 'ALTER TABLE'
    , 'CREATE FOREIGN TABLE', 'ALTER FOREIGN TABLE'
    , 'CREATE VIEW', 'ALTER VIEW'
    , 'CREATE MATERIALIZED VIEW', 'ALTER MATERIALIZED VIEW'
    , 'CREATE FUNCTION', 'ALTER FUNCTION'
    , 'CREATE TRIGGER'
    , 'CREATE TYPE', 'ALTER TYPE'
    , 'CREATE RULE'
    , 'COMMENT'
    )
    -- don't notify in case of CREATE TEMP table or other objects created on pg_temp
    AND cmd.schema_name is distinct from 'pg_temp'
    THEN
      NOTIFY pgrst, 'reload schema';
    END IF;
  END LOOP;
END; $$;


ALTER FUNCTION extensions.pgrst_ddl_watch() OWNER TO supabase_admin;

--
-- Name: pgrst_drop_watch(); Type: FUNCTION; Schema: extensions; Owner: supabase_admin
--

CREATE FUNCTION extensions.pgrst_drop_watch() RETURNS event_trigger
    LANGUAGE plpgsql
    SET search_path TO ''
    AS $$
DECLARE
  obj record;
BEGIN
  FOR obj IN SELECT * FROM pg_event_trigger_dropped_objects()
  LOOP
    IF obj.object_type IN (
      'schema'
    , 'table'
    , 'foreign table'
    , 'view'
    , 'materialized view'
    , 'function'
    , 'trigger'
    , 'type'
    , 'rule'
    )
    AND obj.is_temporary IS false -- no pg_temp objects
    THEN
      NOTIFY pgrst, 'reload schema';
    END IF;
  END LOOP;
END; $$;


ALTER FUNCTION extensions.pgrst_drop_watch() OWNER TO supabase_admin;

--
-- Name: set_graphql_placeholder(); Type: FUNCTION; Schema: extensions; Owner: supabase_admin
--

CREATE FUNCTION extensions.set_graphql_placeholder() RETURNS event_trigger
    LANGUAGE plpgsql
    SET search_path TO ''
    AS $_$
    DECLARE
    graphql_is_dropped bool;
    BEGIN
    graphql_is_dropped = (
        SELECT ev.schema_name = 'graphql_public'
        FROM pg_event_trigger_dropped_objects() AS ev
        WHERE ev.schema_name = 'graphql_public'
    );

    IF graphql_is_dropped
    THEN
        create or replace function graphql_public.graphql(
            "operationName" text default null,
            query text default null,
            variables jsonb default null,
            extensions jsonb default null
        )
            returns jsonb
            language plpgsql
            set search_path to ''
        as $$
            DECLARE
                server_version float;
            BEGIN
                server_version = (SELECT (SPLIT_PART((select version()), ' ', 2))::float);

                IF server_version >= 14 THEN
                    RETURN jsonb_build_object(
                        'errors', jsonb_build_array(
                            jsonb_build_object(
                                'message', 'pg_graphql extension is not enabled.'
                            )
                        )
                    );
                ELSE
                    RETURN jsonb_build_object(
                        'errors', jsonb_build_array(
                            jsonb_build_object(
                                'message', 'pg_graphql is only available on projects running Postgres 14 onwards.'
                            )
                        )
                    );
                END IF;
            END;
        $$;
    END IF;

    END;
$_$;


ALTER FUNCTION extensions.set_graphql_placeholder() OWNER TO supabase_admin;

--
-- Name: FUNCTION set_graphql_placeholder(); Type: COMMENT; Schema: extensions; Owner: supabase_admin
--

COMMENT ON FUNCTION extensions.set_graphql_placeholder() IS 'Reintroduces placeholder function for graphql_public.graphql';


--
-- Name: graphql(text, text, jsonb, jsonb); Type: FUNCTION; Schema: graphql_public; Owner: supabase_admin
--

CREATE FUNCTION graphql_public.graphql("operationName" text DEFAULT NULL::text, query text DEFAULT NULL::text, variables jsonb DEFAULT NULL::jsonb, extensions jsonb DEFAULT NULL::jsonb) RETURNS jsonb
    LANGUAGE plpgsql
    AS $$
            DECLARE
                server_version float;
            BEGIN
                server_version = (SELECT (SPLIT_PART((select version()), ' ', 2))::float);

                IF server_version >= 14 THEN
                    RETURN jsonb_build_object(
                        'errors', jsonb_build_array(
                            jsonb_build_object(
                                'message', 'pg_graphql extension is not enabled.'
                            )
                        )
                    );
                ELSE
                    RETURN jsonb_build_object(
                        'errors', jsonb_build_array(
                            jsonb_build_object(
                                'message', 'pg_graphql is only available on projects running Postgres 14 onwards.'
                            )
                        )
                    );
                END IF;
            END;
        $$;


ALTER FUNCTION graphql_public.graphql("operationName" text, query text, variables jsonb, extensions jsonb) OWNER TO supabase_admin;

--
-- Name: get_auth(text); Type: FUNCTION; Schema: pgbouncer; Owner: supabase_admin
--

CREATE FUNCTION pgbouncer.get_auth(p_usename text) RETURNS TABLE(username text, password text)
    LANGUAGE plpgsql SECURITY DEFINER
    SET search_path TO ''
    AS $_$
  BEGIN
      RAISE DEBUG 'PgBouncer auth request: %', p_usename;

      RETURN QUERY
      SELECT
          rolname::text,
          CASE WHEN rolvaliduntil < now()
              THEN null
              ELSE rolpassword::text
          END
      FROM pg_authid
      WHERE rolname=$1 and rolcanlogin;
  END;
  $_$;


ALTER FUNCTION pgbouncer.get_auth(p_usename text) OWNER TO supabase_admin;

--
-- Name: apply_rls(jsonb, integer); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.apply_rls(wal jsonb, max_record_bytes integer DEFAULT (1024 * 1024)) RETURNS SETOF realtime.wal_rls
    LANGUAGE plpgsql
    AS $$
declare
    -- Regclass of the table e.g. public.notes
    entity_ regclass = (quote_ident(wal ->> 'schema') || '.' || quote_ident(wal ->> 'table'))::regclass;

    -- I, U, D, T: insert, update ...
    action realtime.action = (
        case wal ->> 'action'
            when 'I' then 'INSERT'
            when 'U' then 'UPDATE'
            when 'D' then 'DELETE'
            else 'ERROR'
        end
    );

    -- Is row level security enabled for the table
    is_rls_enabled bool = relrowsecurity from pg_class where oid = entity_;

    subscriptions realtime.subscription[] = array_agg(subs)
        from
            realtime.subscription subs
        where
            subs.entity = entity_
            -- Filter by action early - only get subscriptions interested in this action
            -- action_filter column can be: '*' (all), 'INSERT', 'UPDATE', or 'DELETE'
            and (subs.action_filter = '*' or subs.action_filter = action::text);

    -- Subscription vars
    working_role regrole;
    working_selected_columns text[];
    claimed_role regrole;
    claims jsonb;

    subscription_id uuid;
    subscription_has_access bool;
    visible_to_subscription_ids uuid[] = '{}';

    -- structured info for wal's columns
    columns realtime.wal_column[];
    -- previous identity values for update/delete
    old_columns realtime.wal_column[];

    error_record_exceeds_max_size boolean = octet_length(wal::text) > max_record_bytes;

    -- Primary jsonb output for record
    output jsonb;

    -- Loop record for iterating unique roles (outer loop)
    role_record record;
    -- Loop record for iterating unique selected_columns within a role (inner loop)
    cols_record record;
    -- Subscription ids visible at the role level (before fanning out by selected_columns)
    visible_role_sub_ids uuid[] = '{}';

begin
    perform set_config('role', null, true);

    columns =
        array_agg(
            (
                x->>'name',
                x->>'type',
                x->>'typeoid',
                realtime.cast(
                    (x->'value') #>> '{}',
                    coalesce(
                        (x->>'typeoid')::regtype, -- null when wal2json version <= 2.4
                        (x->>'type')::regtype
                    )
                ),
                (pks ->> 'name') is not null,
                true
            )::realtime.wal_column
        )
        from
            jsonb_array_elements(wal -> 'columns') x
            left join jsonb_array_elements(wal -> 'pk') pks
                on (x ->> 'name') = (pks ->> 'name');

    old_columns =
        array_agg(
            (
                x->>'name',
                x->>'type',
                x->>'typeoid',
                realtime.cast(
                    (x->'value') #>> '{}',
                    coalesce(
                        (x->>'typeoid')::regtype, -- null when wal2json version <= 2.4
                        (x->>'type')::regtype
                    )
                ),
                (pks ->> 'name') is not null,
                true
            )::realtime.wal_column
        )
        from
            jsonb_array_elements(wal -> 'identity') x
            left join jsonb_array_elements(wal -> 'pk') pks
                on (x ->> 'name') = (pks ->> 'name');

    for role_record in
        select claims_role
        from (select distinct claims_role from unnest(subscriptions)) t
        order by claims_role::text
    loop
        working_role := role_record.claims_role;

        -- Update `is_selectable` for columns and old_columns (once per role)
        columns =
            array_agg(
                (
                    c.name,
                    c.type_name,
                    c.type_oid,
                    c.value,
                    c.is_pkey,
                    pg_catalog.has_column_privilege(working_role, entity_, c.name, 'SELECT')
                )::realtime.wal_column
            )
            from
                unnest(columns) c;

        old_columns =
                array_agg(
                    (
                        c.name,
                        c.type_name,
                        c.type_oid,
                        c.value,
                        c.is_pkey,
                        pg_catalog.has_column_privilege(working_role, entity_, c.name, 'SELECT')
                    )::realtime.wal_column
                )
                from
                    unnest(old_columns) c;

        if action <> 'DELETE' and count(1) = 0 from unnest(columns) c where c.is_pkey then
            -- Fan out 400 error per distinct selected_columns for this role
            for cols_record in
                select selected_columns
                from (select distinct selected_columns from unnest(subscriptions) s where s.claims_role = working_role) t
                order by coalesce(array_to_string(selected_columns, ','), '')
            loop
                working_selected_columns := cols_record.selected_columns;
                return next (
                    jsonb_build_object(
                        'schema', wal ->> 'schema',
                        'table', wal ->> 'table',
                        'type', action
                    ),
                    is_rls_enabled,
                    (select array_agg(s.subscription_id) from unnest(subscriptions) as s where s.claims_role = working_role and (s.selected_columns is not distinct from working_selected_columns)),
                    array['Error 400: Bad Request, no primary key']
                )::realtime.wal_rls;
            end loop;

        -- The claims role does not have SELECT permission to the primary key of entity
        elsif action <> 'DELETE' and sum(c.is_selectable::int) <> count(1) from unnest(columns) c where c.is_pkey then
            -- Fan out 401 error per distinct selected_columns for this role
            for cols_record in
                select selected_columns
                from (select distinct selected_columns from unnest(subscriptions) s where s.claims_role = working_role) t
                order by coalesce(array_to_string(selected_columns, ','), '')
            loop
                working_selected_columns := cols_record.selected_columns;
                return next (
                    jsonb_build_object(
                        'schema', wal ->> 'schema',
                        'table', wal ->> 'table',
                        'type', action
                    ),
                    is_rls_enabled,
                    (select array_agg(s.subscription_id) from unnest(subscriptions) as s where s.claims_role = working_role and (s.selected_columns is not distinct from working_selected_columns)),
                    array['Error 401: Unauthorized']
                )::realtime.wal_rls;
            end loop;

        else
            -- Create the prepared statement (once per role)
            if is_rls_enabled and action <> 'DELETE' then
                if (select 1 from pg_prepared_statements where name = 'walrus_rls_stmt' limit 1) > 0 then
                    deallocate walrus_rls_stmt;
                end if;
                execute realtime.build_prepared_statement_sql('walrus_rls_stmt', entity_, columns);
            end if;

            -- Collect all visible subscription IDs for this role (filter check + RLS check)
            visible_role_sub_ids = '{}';

            for subscription_id, claims in (
                    select
                        subs.subscription_id,
                        subs.claims
                    from
                        unnest(subscriptions) subs
                    where
                        subs.entity = entity_
                        and subs.claims_role = working_role
                        and (
                            realtime.is_visible_through_filters(columns, subs.filters)
                            or (
                              action = 'DELETE'
                              and realtime.is_visible_through_filters(old_columns, subs.filters)
                            )
                        )
            ) loop

                if not is_rls_enabled or action = 'DELETE' then
                    visible_role_sub_ids = visible_role_sub_ids || subscription_id;
                else
                    -- Check if RLS allows the role to see the record
                    perform
                        -- Trim leading and trailing quotes from working_role because set_config
                        -- doesn't recognize the role as valid if they are included
                        set_config('role', trim(both '"' from working_role::text), true),
                        set_config('request.jwt.claims', claims::text, true);

                    execute 'execute walrus_rls_stmt' into subscription_has_access;

                    -- Reset the role on every FOR..LOOP batch execution.
                    -- The first batch of 10 rows is pre-fetched using the current connection role (PG internal behaviour)
                    -- then we have to reset it again otherwise it would use the role defined in the `set_config` above
                    -- to fetch the remaining rows when rows>10, which could be a user-defined role that lacks execution grants.
                    -- The flow is:
                    --   1. run batch with conn role
                    --   2. set_config working_role
                    --   3. execute walrus
                    --   4. reset role (revert)
                    --   5. repeat
                    perform set_config('role', null, true);

                    if subscription_has_access then
                        visible_role_sub_ids = visible_role_sub_ids || subscription_id;
                    end if;
                end if;
            end loop;

            perform set_config('role', null, true);

            -- Inner loop: per distinct selected_columns for this role
            for cols_record in
                select selected_columns
                from (select distinct selected_columns from unnest(subscriptions) s where s.claims_role = working_role) t
                order by coalesce(array_to_string(selected_columns, ','), '')
            loop
                working_selected_columns := cols_record.selected_columns;

                output = jsonb_build_object(
                    'schema', wal ->> 'schema',
                    'table', wal ->> 'table',
                    'type', action,
                    'commit_timestamp', to_char(
                        ((wal ->> 'timestamp')::timestamptz at time zone 'utc'),
                        'YYYY-MM-DD"T"HH24:MI:SS.MS"Z"'
                    ),
                    'columns', (
                        select
                            jsonb_agg(
                                jsonb_build_object(
                                    'name', pa.attname,
                                    'type', pt.typname
                                )
                                order by pa.attnum asc
                            )
                        from
                            pg_attribute pa
                            join pg_type pt
                                on pa.atttypid = pt.oid
                            left join (
                                select unnest(conkey) as pkey_attnum
                                from pg_constraint
                                where conrelid = entity_ and contype = 'p'
                            ) pk on pk.pkey_attnum = pa.attnum
                        where
                            attrelid = entity_
                            and attnum > 0
                            and pg_catalog.has_column_privilege(working_role, entity_, pa.attname, 'SELECT')
                            and (working_selected_columns is null or pa.attname = any(working_selected_columns) or pk.pkey_attnum is not null)
                    )
                )
                -- Add "record" key for insert and update
                || case
                    when action in ('INSERT', 'UPDATE') then
                        jsonb_build_object(
                            'record',
                            (
                                select
                                    jsonb_object_agg(
                                        -- if unchanged toast, get column name and value from old record
                                        coalesce((c).name, (oc).name),
                                        case
                                            when (c).name is null then (oc).value
                                            else (c).value
                                        end
                                    )
                                from
                                    unnest(columns) c
                                    full outer join unnest(old_columns) oc
                                        on (c).name = (oc).name
                                where
                                    coalesce((c).is_selectable, (oc).is_selectable)
                                    and (working_selected_columns is null or coalesce((c).name, (oc).name) = any(working_selected_columns) or coalesce((c).is_pkey, (oc).is_pkey))
                                    and ( not error_record_exceeds_max_size or (octet_length((c).value::text) <= 64))
                            )
                        )
                    else '{}'::jsonb
                end
                -- Add "old_record" key for update and delete
                || case
                    when action = 'UPDATE' then
                        jsonb_build_object(
                                'old_record',
                                (
                                    select jsonb_object_agg((c).name, (c).value)
                                    from unnest(old_columns) c
                                    where
                                        (c).is_selectable
                                        and (working_selected_columns is null or (c).name = any(working_selected_columns) or (c).is_pkey)
                                        and ( not error_record_exceeds_max_size or (octet_length((c).value::text) <= 64))
                                )
                            )
                    when action = 'DELETE' then
                        jsonb_build_object(
                            'old_record',
                            (
                                select jsonb_object_agg((c).name, (c).value)
                                from unnest(old_columns) c
                                where
                                    (c).is_selectable
                                    and (working_selected_columns is null or (c).name = any(working_selected_columns) or (c).is_pkey)
                                    and ( not error_record_exceeds_max_size or (octet_length((c).value::text) <= 64))
                                    and ( not is_rls_enabled or (c).is_pkey ) -- if RLS enabled, we can't secure deletes so filter to pkey
                            )
                        )
                    else '{}'::jsonb
                end;

                -- Filter visible_role_sub_ids to those matching the current selected_columns group
                visible_to_subscription_ids = coalesce(
                    (
                        select array_agg(s.subscription_id)
                        from unnest(subscriptions) s
                        where s.claims_role = working_role
                          and (s.selected_columns is not distinct from working_selected_columns)
                          and s.subscription_id = any(visible_role_sub_ids)
                    ),
                    '{}'::uuid[]
                );

                return next (
                    output,
                    is_rls_enabled,
                    visible_to_subscription_ids,
                    case
                        when error_record_exceeds_max_size then array['Error 413: Payload Too Large']
                        else '{}'
                    end
                )::realtime.wal_rls;
            end loop;

        end if;
    end loop;

    perform set_config('role', null, true);
end;
$$;


ALTER FUNCTION realtime.apply_rls(wal jsonb, max_record_bytes integer) OWNER TO supabase_realtime_admin;

--
-- Name: broadcast_changes(text, text, text, text, text, record, record, text); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.broadcast_changes(topic_name text, event_name text, operation text, table_name text, table_schema text, new record, old record, level text DEFAULT 'ROW'::text) RETURNS void
    LANGUAGE plpgsql
    AS $$
DECLARE
    -- Declare a variable to hold the JSONB representation of the row
    row_data jsonb := '{}'::jsonb;
BEGIN
    IF level = 'STATEMENT' THEN
        RAISE EXCEPTION 'function can only be triggered for each row, not for each statement';
    END IF;
    -- Check the operation type and handle accordingly
    IF operation = 'INSERT' OR operation = 'UPDATE' OR operation = 'DELETE' THEN
        row_data := jsonb_build_object('old_record', OLD, 'record', NEW, 'operation', operation, 'table', table_name, 'schema', table_schema);
        PERFORM realtime.send (row_data, event_name, topic_name);
    ELSE
        RAISE EXCEPTION 'Unexpected operation type: %', operation;
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE EXCEPTION 'Failed to process the row: %', SQLERRM;
END;

$$;


ALTER FUNCTION realtime.broadcast_changes(topic_name text, event_name text, operation text, table_name text, table_schema text, new record, old record, level text) OWNER TO supabase_realtime_admin;

--
-- Name: build_prepared_statement_sql(text, regclass, realtime.wal_column[]); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.build_prepared_statement_sql(prepared_statement_name text, entity regclass, columns realtime.wal_column[]) RETURNS text
    LANGUAGE sql
    AS $$
      /*
      Builds a sql string that, if executed, creates a prepared statement to
      tests retrive a row from *entity* by its primary key columns.
      Example
          select realtime.build_prepared_statement_sql('public.notes', '{"id"}'::text[], '{"bigint"}'::text[])
      */
          select
      'prepare ' || prepared_statement_name || ' as
          select
              exists(
                  select
                      1
                  from
                      ' || entity || '
                  where
                      ' || string_agg(quote_ident(pkc.name) || '=' || quote_nullable(pkc.value #>> '{}') , ' and ') || '
              )'
          from
              unnest(columns) pkc
          where
              pkc.is_pkey
          group by
              entity
      $$;


ALTER FUNCTION realtime.build_prepared_statement_sql(prepared_statement_name text, entity regclass, columns realtime.wal_column[]) OWNER TO supabase_realtime_admin;

--
-- Name: cast(text, regtype); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime."cast"(val text, type_ regtype) RETURNS jsonb
    LANGUAGE plpgsql IMMUTABLE
    AS $$
declare
  res jsonb;
begin
  if type_::text = 'bytea' then
    return to_jsonb(val);
  end if;
  execute format('select to_jsonb(%L::'|| type_::text || ')', val) into res;
  return res;
end
$$;


ALTER FUNCTION realtime."cast"(val text, type_ regtype) OWNER TO supabase_realtime_admin;

--
-- Name: check_equality_op(realtime.equality_op, regtype, text, text); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text) RETURNS boolean
    LANGUAGE plpgsql IMMUTABLE
    AS $$
/*
Casts *val_1* and *val_2* as type *type_* and check the *op* condition for truthiness
*/
declare
    op_symbol text = (
        case
            when op = 'eq' then '='
            when op = 'neq' then '!='
            when op = 'lt' then '<'
            when op = 'lte' then '<='
            when op = 'gt' then '>'
            when op = 'gte' then '>='
            when op = 'in' then '= any'
            else 'UNKNOWN OP'
        end
    );
    res boolean;
begin
    execute format(
        'select %L::'|| type_::text || ' ' || op_symbol
        || ' ( %L::'
        || (
            case
                when op = 'in' then type_::text || '[]'
                else type_::text end
        )
        || ')', val_1, val_2) into res;
    return res;
end;
$$;


ALTER FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text) OWNER TO supabase_realtime_admin;

--
-- Name: check_equality_op(realtime.equality_op, regtype, text, text, boolean); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text, negate boolean) RETURNS boolean
    LANGUAGE plpgsql STABLE
    AS $$
declare
    op_symbol text;
    res boolean;
begin
    -- IS DISTINCT FROM / IS NOT DISTINCT FROM: infix, both sides typed literals
    if op = 'isdistinct' then
        execute format(
            'select %L::%s %s %L::%s',
            val_1,
            type_::text,
            case when negate then 'IS NOT DISTINCT FROM' else 'IS DISTINCT FROM' end,
            val_2,
            type_::text
        ) into res;
        return res;
    end if;

    -- IS requires a keyword RHS (NULL, TRUE, FALSE, UNKNOWN), not a typed literal
    if op = 'is' then
        if val_2 not in ('null', 'true', 'false', 'unknown') then
            raise exception 'invalid value for is filter: must be null, true, false, or unknown';
        end if;
        execute format(
            'select %L::%s %s %s',
            val_1,
            type_::text,
            case when negate then 'IS NOT' else 'IS' end,
            upper(val_2)
        ) into res;
        return res;
    end if;

    op_symbol = case
        when op = 'eq'    then '='
        when op = 'neq'   then '!='
        when op = 'lt'    then '<'
        when op = 'lte'   then '<='
        when op = 'gt'    then '>'
        when op = 'gte'   then '>='
        when op = 'in'    then '= any'
        when op = 'like'   then 'LIKE'
        when op = 'ilike'  then 'ILIKE'
        when op = 'match'  then '~'
        when op = 'imatch' then '~*'
        else null
    end;

    if op_symbol is null then
        raise exception 'unsupported equality operator: %', op::text;
    end if;

    execute format(
        'select %L::%s %s (%L::%s)',
        val_1,
        type_::text,
        op_symbol,
        val_2,
        case when op = 'in' then type_::text || '[]' else type_::text end
    ) into res;

    return case when negate then not res else res end;
end;
$$;


ALTER FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text, negate boolean) OWNER TO supabase_realtime_admin;

--
-- Name: is_visible_through_filters(realtime.wal_column[], realtime.user_defined_filter[]); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.is_visible_through_filters(columns realtime.wal_column[], filters realtime.user_defined_filter[]) RETURNS boolean
    LANGUAGE sql STABLE
    AS $$
    select
        filters is null
        or array_length(filters, 1) is null
        or coalesce(
            count(col.name) = count(1)
            and sum(
                realtime.check_equality_op(
                    op:=f.op,
                    type_:=coalesce(col.type_oid::regtype, col.type_name::regtype),
                    val_1:=col.value #>> '{}',
                    val_2:=f.value,
                    negate:=coalesce(f.negate, false)
                )::int
            ) filter (where col.name is not null) = count(col.name),
            false
        )
    from
        unnest(filters) f
        left join unnest(columns) col
            on f.column_name = col.name;
$$;


ALTER FUNCTION realtime.is_visible_through_filters(columns realtime.wal_column[], filters realtime.user_defined_filter[]) OWNER TO supabase_realtime_admin;

--
-- Name: list_changes(name, name, integer, integer); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.list_changes(publication name, slot_name name, max_changes integer, max_record_bytes integer) RETURNS TABLE(wal jsonb, is_rls_enabled boolean, subscription_ids uuid[], errors text[], slot_changes_count bigint)
    LANGUAGE sql
    SET log_min_messages TO 'fatal'
    AS $$
  WITH pub AS (
    SELECT
      concat_ws(
        ',',
        CASE WHEN bool_or(pubinsert) THEN 'insert' ELSE NULL END,
        CASE WHEN bool_or(pubupdate) THEN 'update' ELSE NULL END,
        CASE WHEN bool_or(pubdelete) THEN 'delete' ELSE NULL END
      ) AS w2j_actions,
      coalesce(
        string_agg(
          realtime.quote_wal2json(format('%I.%I', schemaname, tablename)::regclass),
          ','
        ) filter (WHERE ppt.tablename IS NOT NULL),
        ''
      ) AS w2j_add_tables
    FROM pg_publication pp
    LEFT JOIN pg_publication_tables ppt ON pp.pubname = ppt.pubname
    WHERE pp.pubname = publication
    GROUP BY pp.pubname
    LIMIT 1
  ),
  -- MATERIALIZED ensures pg_logical_slot_get_changes is called exactly once
  w2j AS MATERIALIZED (
    SELECT x.*, pub.w2j_add_tables
    FROM pub,
         pg_logical_slot_get_changes(
           slot_name, null, max_changes,
           'include-pk', 'true',
           'include-transaction', 'false',
           'include-timestamp', 'true',
           'include-type-oids', 'true',
           'format-version', '2',
           'actions', pub.w2j_actions,
           'add-tables', pub.w2j_add_tables
         ) x
  ),
  slot_count AS (
    SELECT count(*)::bigint AS cnt
    FROM w2j
    WHERE w2j.w2j_add_tables <> ''
  ),
  rls_filtered AS (
    SELECT xyz.wal, xyz.is_rls_enabled, xyz.subscription_ids, xyz.errors
    FROM w2j,
         realtime.apply_rls(
           wal := w2j.data::jsonb,
           max_record_bytes := max_record_bytes
         ) xyz(wal, is_rls_enabled, subscription_ids, errors)
    WHERE w2j.w2j_add_tables <> ''
      AND xyz.subscription_ids[1] IS NOT NULL
  )
  SELECT rf.wal, rf.is_rls_enabled, rf.subscription_ids, rf.errors, sc.cnt
  FROM rls_filtered rf, slot_count sc

  UNION ALL

  SELECT null, null, null, null, sc.cnt
  FROM slot_count sc
  WHERE NOT EXISTS (SELECT 1 FROM rls_filtered)
$$;


ALTER FUNCTION realtime.list_changes(publication name, slot_name name, max_changes integer, max_record_bytes integer) OWNER TO supabase_realtime_admin;

--
-- Name: quote_wal2json(regclass); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.quote_wal2json(entity regclass) RETURNS text
    LANGUAGE sql IMMUTABLE STRICT
    AS $$
  SELECT
    realtime.wal2json_escape_identifier(nsp.nspname::text)
    || '.'
    || realtime.wal2json_escape_identifier(pc.relname::text)
  FROM pg_class pc
  JOIN pg_namespace nsp ON pc.relnamespace = nsp.oid
  WHERE pc.oid = entity
$$;


ALTER FUNCTION realtime.quote_wal2json(entity regclass) OWNER TO supabase_realtime_admin;

--
-- Name: send(jsonb, text, text, boolean); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.send(payload jsonb, event text, topic text, private boolean DEFAULT true) RETURNS void
    LANGUAGE plpgsql
    AS $$
DECLARE
  generated_id uuid;
  final_payload jsonb;
BEGIN
  BEGIN
    generated_id := gen_random_uuid();

    -- Check if payload has an 'id' key, if not, add the generated UUID
    IF payload ? 'id' THEN
      final_payload := payload;
    ELSE
      final_payload := jsonb_set(payload, '{id}', to_jsonb(generated_id));
    END IF;

    -- Set the topic configuration
    EXECUTE format('SET LOCAL realtime.topic TO %L', topic);

    INSERT INTO realtime.messages (id, payload, event, topic, private, extension)
    VALUES (generated_id, final_payload, event, topic, private, 'broadcast');
  EXCEPTION
    WHEN OTHERS THEN
      RAISE WARNING 'WarnSendingBroadcastMessage: %', SQLERRM;
  END;
END;
$$;


ALTER FUNCTION realtime.send(payload jsonb, event text, topic text, private boolean) OWNER TO supabase_realtime_admin;

--
-- Name: send_binary(bytea, text, text, boolean); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.send_binary(payload bytea, event text, topic text, private boolean DEFAULT true) RETURNS void
    LANGUAGE plpgsql
    AS $$
DECLARE
  generated_id uuid;
BEGIN
  BEGIN
    generated_id := gen_random_uuid();

    EXECUTE format('SET LOCAL realtime.topic TO %L', topic);

    INSERT INTO realtime.messages (id, binary_payload, event, topic, private, extension)
    VALUES (generated_id, payload, event, topic, private, 'broadcast');
  EXCEPTION
    WHEN OTHERS THEN
      RAISE WARNING 'WarnSendingBroadcastMessage: %', SQLERRM;
  END;
END;
$$;


ALTER FUNCTION realtime.send_binary(payload bytea, event text, topic text, private boolean) OWNER TO supabase_realtime_admin;

--
-- Name: subscription_check_filters(); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.subscription_check_filters() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare
    col_names text[] = coalesce(
            array_agg(a.attname order by a.attnum),
            '{}'::text[]
        )
        from
            pg_catalog.pg_attribute a
        where
            a.attrelid = new.entity
            and a.attnum > 0
            and not a.attisdropped
            and pg_catalog.has_column_privilege(
                (new.claims ->> 'role'),
                a.attrelid,
                a.attnum,
                'SELECT'
            );
    filter realtime.user_defined_filter;
    col_type regtype;
    in_val jsonb;
    selected_col text;
begin
    for filter in select * from unnest(new.filters) loop
        if not filter.column_name = any(col_names) then
            raise exception 'invalid column for filter %', filter.column_name;
        end if;

        col_type = (
            select atttypid::regtype
            from pg_catalog.pg_attribute
            where attrelid = new.entity
                  and attname = filter.column_name
        );
        if col_type is null then
            raise exception 'failed to lookup type for column %', filter.column_name;
        end if;

        if filter.op = 'in'::realtime.equality_op then
            in_val = realtime.cast(filter.value, (col_type::text || '[]')::regtype);
            if coalesce(jsonb_array_length(in_val), 0) > 100 then
                raise exception 'too many values for `in` filter. Maximum 100';
            end if;
        elsif filter.op = 'is'::realtime.equality_op then
            -- `is` requires a keyword RHS rather than a typed literal
            if filter.value not in ('null', 'true', 'false', 'unknown') then
                raise exception 'invalid value for is filter: must be null, true, false, or unknown';
            end if;
            -- IS NULL works for any type, but IS TRUE/FALSE/UNKNOWN require a boolean
            -- operand. Reject the non-null keywords on non-boolean columns here so they
            -- don't abort apply_rls at WAL time.
            if filter.value <> 'null' and col_type <> 'boolean'::regtype then
                raise exception 'is % filter requires a boolean column, got %', filter.value, col_type::text;
            end if;
        elsif filter.op in ('like'::realtime.equality_op, 'ilike'::realtime.equality_op) then
            -- like/ilike apply the text pattern operator (~~); reject column types that
            -- have no such operator instead of failing at WAL time
            if not exists (
                select 1 from pg_catalog.pg_operator
                where oprname = '~~' and oprleft = col_type
            ) then
                raise exception 'operator % requires a text-compatible column type, got %', filter.op::text, col_type::text;
            end if;
        elsif filter.op in ('match'::realtime.equality_op, 'imatch'::realtime.equality_op) then
            -- match/imatch apply the regex operators ~ / ~*; reject column types that have
            -- no such operator (e.g. integer) instead of failing at WAL time, mirroring the
            -- like/ilike guard above.
            if not exists (
                select 1 from pg_catalog.pg_operator
                where oprname = case when filter.op = 'imatch'::realtime.equality_op then '~*' else '~' end
                  and oprleft = col_type
                  and oprright = col_type
                  and oprresult = 'boolean'::regtype
            ) then
                raise exception 'operator % requires a text-compatible column type, got %', filter.op::text, col_type::text;
            end if;
            -- validate the regex eagerly so a bad pattern is rejected here, not inside
            -- apply_rls where it would abort the WAL stream for the entity
            begin
                perform '' ~ filter.value;
            exception when others then
                raise exception 'invalid regular expression for % filter: %', filter.op::text, sqlerrm;
            end;
        else
            -- eq/neq/lt/lte/gt/gte: value must be coercable to the type
            perform realtime.cast(filter.value, col_type);
        end if;
    end loop;

    if new.selected_columns is not null then
        for selected_col in select * from unnest(new.selected_columns) loop
            if not selected_col = any(col_names) then
                raise exception 'invalid column for select %', selected_col;
            end if;
        end loop;
    end if;

    -- Apply consistent order to filters so the unique constraint can't be tricked by a
    -- different filter order. negate is part of the sort key.
    new.filters = coalesce(
        array_agg(f order by f.column_name, f.op, f.value, f.negate),
        '{}'
    ) from unnest(new.filters) f;

    -- Normalize selected_columns order so ARRAY['a','b'] and ARRAY['b','a'] are treated
    -- as the same subscription group in apply_rls. Preserve an empty array as '{}'
    -- ("primary keys only") so it stays distinct from NULL ("all columns"); array_agg
    -- over an empty set would otherwise collapse '{}' back to NULL.
    if new.selected_columns is not null then
        new.selected_columns = coalesce(
            (
                select array_agg(c order by c)
                from unnest(new.selected_columns) c
            ),
            '{}'::text[]
        );
    end if;

    return new;
end;
$$;


ALTER FUNCTION realtime.subscription_check_filters() OWNER TO supabase_realtime_admin;

--
-- Name: to_regrole(text); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.to_regrole(role_name text) RETURNS regrole
    LANGUAGE sql IMMUTABLE
    AS $$ select role_name::regrole $$;


ALTER FUNCTION realtime.to_regrole(role_name text) OWNER TO supabase_realtime_admin;

--
-- Name: topic(); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.topic() RETURNS text
    LANGUAGE sql STABLE
    AS $$
select nullif(current_setting('realtime.topic', true), '')::text;
$$;


ALTER FUNCTION realtime.topic() OWNER TO supabase_realtime_admin;

--
-- Name: wal2json_escape_identifier(text); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.wal2json_escape_identifier(name text) RETURNS text
    LANGUAGE sql IMMUTABLE STRICT
    AS $$
  -- Prefix `\`, `,`, `.`, and any whitespace with `\`
  SELECT regexp_replace(name, '([\\,.[:space:]])', '\\\1', 'g')
$$;


ALTER FUNCTION realtime.wal2json_escape_identifier(name text) OWNER TO supabase_realtime_admin;

--
-- Name: allow_any_operation(text[]); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.allow_any_operation(expected_operations text[]) RETURNS boolean
    LANGUAGE sql STABLE
    AS $$
  WITH current_operation AS (
    SELECT storage.operation() AS raw_operation
  ),
  normalized AS (
    SELECT CASE
      WHEN raw_operation LIKE 'storage.%' THEN substr(raw_operation, 9)
      ELSE raw_operation
    END AS current_operation
    FROM current_operation
  )
  SELECT EXISTS (
    SELECT 1
    FROM normalized n
    CROSS JOIN LATERAL unnest(expected_operations) AS expected_operation
    WHERE expected_operation IS NOT NULL
      AND expected_operation <> ''
      AND n.current_operation = CASE
        WHEN expected_operation LIKE 'storage.%' THEN substr(expected_operation, 9)
        ELSE expected_operation
      END
  );
$$;


ALTER FUNCTION storage.allow_any_operation(expected_operations text[]) OWNER TO supabase_storage_admin;

--
-- Name: allow_only_operation(text); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.allow_only_operation(expected_operation text) RETURNS boolean
    LANGUAGE sql STABLE
    AS $$
  WITH current_operation AS (
    SELECT storage.operation() AS raw_operation
  ),
  normalized AS (
    SELECT
      CASE
        WHEN raw_operation LIKE 'storage.%' THEN substr(raw_operation, 9)
        ELSE raw_operation
      END AS current_operation,
      CASE
        WHEN expected_operation LIKE 'storage.%' THEN substr(expected_operation, 9)
        ELSE expected_operation
      END AS requested_operation
    FROM current_operation
  )
  SELECT CASE
    WHEN requested_operation IS NULL OR requested_operation = '' THEN FALSE
    ELSE COALESCE(current_operation = requested_operation, FALSE)
  END
  FROM normalized;
$$;


ALTER FUNCTION storage.allow_only_operation(expected_operation text) OWNER TO supabase_storage_admin;

--
-- Name: can_insert_object(text, text, uuid, jsonb); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.can_insert_object(bucketid text, name text, owner uuid, metadata jsonb) RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN
  INSERT INTO "storage"."objects" ("bucket_id", "name", "owner", "metadata") VALUES (bucketid, name, owner, metadata);
  -- hack to rollback the successful insert
  RAISE sqlstate 'PT200' using
  message = 'ROLLBACK',
  detail = 'rollback successful insert';
END
$$;


ALTER FUNCTION storage.can_insert_object(bucketid text, name text, owner uuid, metadata jsonb) OWNER TO supabase_storage_admin;

--
-- Name: enforce_bucket_lifecycle_service_role(); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.enforce_bucket_lifecycle_service_role() RETURNS trigger
    LANGUAGE plpgsql
    SET search_path TO 'pg_catalog'
    AS $$
BEGIN
  IF current_user::text IS DISTINCT FROM TG_ARGV[0]
     AND (
       OLD.lifecycle_configuration IS DISTINCT FROM NEW.lifecycle_configuration
       OR OLD.lifecycle_configuration_generation IS DISTINCT FROM NEW.lifecycle_configuration_generation
     ) THEN
    -- AFTER runs only after caller RLS has accepted the proposed row. The API
    -- recognizes this specific error after rolling back its permission probe;
    -- direct non-service writes still fail and cannot persist the change.
    RAISE EXCEPTION 'bucket control columns may only be changed by the configured storage service role'
      USING ERRCODE = 'PST01',
            SCHEMA = TG_TABLE_SCHEMA,
            TABLE = TG_TABLE_NAME,
            CONSTRAINT = TG_NAME;
  END IF;

  RETURN NULL;
END;
$$;


ALTER FUNCTION storage.enforce_bucket_lifecycle_service_role() OWNER TO supabase_storage_admin;

--
-- Name: enforce_bucket_name_length(); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.enforce_bucket_name_length() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
begin
    if length(new.name) > 100 then
        raise exception 'bucket name "%" is too long (% characters). Max is 100.', new.name, length(new.name);
    end if;
    return new;
end;
$$;


ALTER FUNCTION storage.enforce_bucket_name_length() OWNER TO supabase_storage_admin;

--
-- Name: extension(text); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.extension(name text) RETURNS text
    LANGUAGE plpgsql IMMUTABLE
    AS $$
DECLARE
    _parts text[];
    _filename text;
BEGIN
    -- Split on "/" to get path segments
    SELECT string_to_array(name, '/') INTO _parts;
    -- Get the last path segment (the actual filename)
    SELECT _parts[array_length(_parts, 1)] INTO _filename;
    -- Extract extension: reverse, split on '.', then reverse again
    RETURN reverse(split_part(reverse(_filename), '.', 1));
END
$$;


ALTER FUNCTION storage.extension(name text) OWNER TO supabase_storage_admin;

--
-- Name: filename(text); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.filename(name text) RETURNS text
    LANGUAGE plpgsql IMMUTABLE
    AS $$
DECLARE
    _parts text[];
BEGIN
    SELECT string_to_array(name, '/') INTO _parts;
    RETURN _parts[array_length(_parts, 1)];
END
$$;


ALTER FUNCTION storage.filename(name text) OWNER TO supabase_storage_admin;

--
-- Name: foldername(text); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.foldername(name text) RETURNS text[]
    LANGUAGE plpgsql IMMUTABLE
    AS $$
DECLARE
    _parts text[];
BEGIN
    -- Split on "/" to get path segments
    SELECT string_to_array(name, '/') INTO _parts;
    -- Return everything except the last segment
    RETURN _parts[1 : array_length(_parts,1) - 1];
END
$$;


ALTER FUNCTION storage.foldername(name text) OWNER TO supabase_storage_admin;

--
-- Name: get_common_prefix(text, text, text); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.get_common_prefix(p_key text, p_prefix text, p_delimiter text) RETURNS text
    LANGUAGE sql IMMUTABLE
    AS $$
SELECT CASE
    WHEN p_delimiter <> ''
         AND position(p_delimiter IN substring(p_key FROM length(p_prefix) + 1)) > 0
    THEN left(
        p_key,
        length(p_prefix)
            + position(p_delimiter IN substring(p_key FROM length(p_prefix) + 1))
            + length(p_delimiter) - 1
    )
    ELSE NULL
END;
$$;


ALTER FUNCTION storage.get_common_prefix(p_key text, p_prefix text, p_delimiter text) OWNER TO supabase_storage_admin;

--
-- Name: get_size_by_bucket(text, text); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.get_size_by_bucket(noncurrent_versions text DEFAULT 'include'::text, delete_markers text DEFAULT 'include'::text) RETURNS TABLE(size bigint, bucket_id text)
    LANGUAGE plpgsql STABLE
    AS $$
BEGIN
    -- COALESCE first: NULL NOT IN (...) evaluates to NULL (not TRUE), so a
    -- bare NOT IN check silently leaves an explicit NULL argument unreset.
    noncurrent_versions := COALESCE(noncurrent_versions, 'include');
    delete_markers := COALESCE(delete_markers, 'include');
    IF noncurrent_versions NOT IN ('exclude', 'only', 'include') THEN
        noncurrent_versions := 'include';
    END IF;
    IF delete_markers NOT IN ('exclude', 'only', 'include') THEN
        delete_markers := 'include';
    END IF;

    return query
        select sum((metadata->>'size')::bigint)::bigint as size, obj.bucket_id
        from "storage".objects as obj
        where (noncurrent_versions != 'exclude' OR obj.archived_at IS NULL)
          and (noncurrent_versions != 'only' OR obj.archived_at IS NOT NULL)
          and (delete_markers != 'exclude' OR NOT obj.is_delete_marker)
          and (delete_markers != 'only' OR obj.is_delete_marker)
        group by obj.bucket_id;
END
$$;


ALTER FUNCTION storage.get_size_by_bucket(noncurrent_versions text, delete_markers text) OWNER TO supabase_storage_admin;

--
-- Name: list_multipart_uploads_with_delimiter(text, text, text, integer, text, text, text); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.list_multipart_uploads_with_delimiter(bucket_id text, prefix_param text, delimiter_param text, max_keys integer DEFAULT 100, next_key_token text DEFAULT ''::text, next_upload_token text DEFAULT ''::text, raw_prefix_param text DEFAULT NULL::text) RETURNS TABLE(key text, id text, created_at timestamp with time zone)
    LANGUAGE sql STABLE
    AS $_$
WITH candidates AS (
    SELECT
        upload.key AS object_key,
        CASE
            WHEN position($3 IN substring(upload.key FROM length(coalesce($7, $2)) + 1)) > 0
            THEN left(
                upload.key,
                length(coalesce($7, $2))
                    + position($3 IN substring(upload.key FROM length(coalesce($7, $2)) + 1))
                    + length($3) - 1
            )
            ELSE upload.key
        END AS result_key,
        upload.id,
        upload.created_at,
        position($3 IN substring(upload.key FROM length(coalesce($7, $2)) + 1)) > 0 AS is_common_prefix
    FROM storage.s3_multipart_uploads AS upload
    WHERE upload.bucket_id = $1
      AND upload.key COLLATE "C" LIKE $2 || '%'
), filtered AS (
    SELECT candidate.*
    FROM candidates AS candidate
    WHERE $5 = ''
       OR candidate.result_key COLLATE "C" > $5
       OR (
           candidate.result_key COLLATE "C" = $5
           AND NOT candidate.is_common_prefix
           AND $6 <> ''
           -- A completed or aborted marker repeats the remaining same-key uploads.
           AND COALESCE(
               (candidate.created_at, candidate.id COLLATE "C") > (
                   SELECT marker.created_at, marker.id COLLATE "C"
                   FROM storage.s3_multipart_uploads AS marker
                   WHERE marker.bucket_id = $1
                     AND marker.key COLLATE "C" = $5
                     AND marker.id = $6
               ),
               TRUE
           )
       )
), ranked AS (
    SELECT
        filtered.*,
        row_number() OVER (
            PARTITION BY filtered.result_key COLLATE "C"
            ORDER BY filtered.created_at, filtered.id COLLATE "C"
        ) AS prefix_rank
    FROM filtered
)
SELECT ranked.result_key, ranked.id, ranked.created_at
FROM ranked
WHERE NOT ranked.is_common_prefix OR ranked.prefix_rank = 1
ORDER BY ranked.result_key COLLATE "C", ranked.created_at, ranked.id COLLATE "C"
LIMIT $4;
$_$;


ALTER FUNCTION storage.list_multipart_uploads_with_delimiter(bucket_id text, prefix_param text, delimiter_param text, max_keys integer, next_key_token text, next_upload_token text, raw_prefix_param text) OWNER TO supabase_storage_admin;

--
-- Name: list_objects_with_delimiter(text, text, text, integer, text, text, text, text, text, timestamp with time zone, text); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.list_objects_with_delimiter(_bucket_id text, prefix_param text, delimiter_param text, max_keys integer DEFAULT 100, start_after text DEFAULT ''::text, next_token text DEFAULT ''::text, sort_order text DEFAULT 'asc'::text, noncurrent_versions text DEFAULT 'exclude'::text, delete_markers text DEFAULT 'exclude'::text, next_token_archived_at timestamp with time zone DEFAULT NULL::timestamp with time zone, next_token_version text DEFAULT ''::text) RETURNS TABLE(name text, id uuid, metadata jsonb, updated_at timestamp with time zone, created_at timestamp with time zone, last_accessed_at timestamp with time zone, version text, archived_at timestamp with time zone, is_delete_marker boolean, is_versioned boolean)
    LANGUAGE plpgsql STABLE
    AS $_$
DECLARE
    v_peek_name TEXT;
    v_current RECORD;
    v_common_prefix TEXT;

    -- Configuration
    v_is_asc BOOLEAN;
    v_prefix TEXT;
    v_start TEXT;
    v_start_relative TEXT;
    v_upper_bound TEXT;
    v_file_batch_size INT;
    v_version_filter TEXT;

    -- true when noncurrent_versions can return >1 row per name; keeps them
    -- ordered most-recent-first and lets pagination resume mid-key
    v_multi_row BOOLEAN;
    v_name_order TEXT;
    v_exact_range_predicate TEXT;
    v_strict_range_predicate TEXT;
    v_inclusive_range_predicate TEXT;

    -- Seek state for the current name. archived_at is normalized to JavaScript's
    -- millisecond precision and version breaks ties within the same millisecond.
    -- Current rows use 'infinity'; NULL means no tiebreak has been established.
    v_next_seek TEXT;
    v_next_seek_at TIMESTAMPTZ;
    v_next_seek_version TEXT;
    v_next_seek_strict BOOLEAN := false;
    v_cursor_is_folder BOOLEAN;
    v_count INT := 0;
    v_previous_seek TEXT;
    v_previous_seek_at TIMESTAMPTZ;
    v_previous_seek_version TEXT;
    v_previous_count INT;

    -- Dynamic SQL for batch query only
    v_batch_query TEXT;
    v_batch_query_strict TEXT;
    v_delete_marker_peek_query TEXT;
    v_delete_marker_peek_query_strict TEXT;

BEGIN
    -- ========================================================================
    -- INITIALIZATION
    -- ========================================================================
    v_is_asc := lower(coalesce(sort_order, 'asc')) = 'asc';
    v_prefix := coalesce(prefix_param, '');
    v_start := CASE WHEN coalesce(next_token, '') <> '' THEN next_token ELSE coalesce(start_after, '') END;
    v_file_batch_size := LEAST(GREATEST(max_keys * 2, 100), 1000);
    v_next_seek_at := NULL;
    v_next_seek_version := '';

    -- COALESCE first: NULL NOT IN (...) evaluates to NULL (not TRUE), so a
    -- bare NOT IN check silently leaves an explicit NULL argument unreset.
    noncurrent_versions := COALESCE(noncurrent_versions, 'exclude');
    delete_markers := COALESCE(delete_markers, 'exclude');
    IF noncurrent_versions NOT IN ('exclude', 'only', 'include') THEN
        noncurrent_versions := 'exclude';
    END IF;
    IF delete_markers NOT IN ('exclude', 'only', 'include') THEN
        delete_markers := 'exclude';
    END IF;

    v_multi_row := noncurrent_versions IN ('only', 'include');
    v_name_order := CASE WHEN v_is_asc THEN 'ASC' ELSE 'DESC' END;

    v_version_filter := '';
    IF noncurrent_versions = 'exclude' THEN
        v_version_filter := v_version_filter || ' AND o.archived_at IS NULL';
    ELSIF noncurrent_versions = 'only' THEN
        v_version_filter := v_version_filter || ' AND o.archived_at IS NOT NULL';
    END IF;
    IF delete_markers = 'exclude' THEN
        v_version_filter := v_version_filter || ' AND NOT o.is_delete_marker';
    ELSIF delete_markers = 'only' THEN
        v_version_filter := v_version_filter || ' AND o.is_delete_marker';
    END IF;

    -- Calculate upper bound for prefix filtering (bytewise, using COLLATE "C")
    IF v_prefix = '' THEN
        v_upper_bound := NULL;
    ELSE
        v_upper_bound := left(v_prefix, -1) || chr(ascii(right(v_prefix, 1)) + 1);
    END IF;

    -- Keep caller-provided cursors inside the requested prefix range.
    IF v_start <> '' AND v_upper_bound IS NOT NULL THEN
        IF v_is_asc THEN
            IF v_start COLLATE "C" < v_prefix COLLATE "C" THEN
                v_start := '';
            ELSIF v_start COLLATE "C" >= v_upper_bound COLLATE "C" THEN
                RETURN;
            END IF;
        ELSE
            IF v_start COLLATE "C" < v_prefix COLLATE "C" THEN
                RETURN;
            ELSIF v_start COLLATE "C" >= v_upper_bound COLLATE "C" THEN
                v_start := '';
            END IF;
        END IF;
    END IF;

    v_start_relative := substring(v_start FROM length(v_prefix) + 1);

    -- Direction affects only the indexed name range and its ordering. Cursor
    -- state transitions and within-key version ordering stay shared.
    IF v_is_asc THEN
        v_exact_range_predicate := 'TRUE';
        v_strict_range_predicate := 'o.name COLLATE "C" > $2';
        v_inclusive_range_predicate := 'o.name COLLATE "C" >= $2';
        IF v_upper_bound IS NOT NULL THEN
            v_exact_range_predicate := 'o.name COLLATE "C" < $3';
            v_strict_range_predicate := v_strict_range_predicate || ' AND o.name COLLATE "C" < $3';
            v_inclusive_range_predicate := v_inclusive_range_predicate || ' AND o.name COLLATE "C" < $3';
        END IF;
    ELSE
        v_exact_range_predicate := 'TRUE';
        v_strict_range_predicate := 'o.name COLLATE "C" < $2';
        v_inclusive_range_predicate := 'o.name COLLATE "C" < $2';
        IF v_prefix <> '' THEN
            v_exact_range_predicate := 'o.name COLLATE "C" >= $3';
            v_strict_range_predicate := v_strict_range_predicate || ' AND o.name COLLATE "C" >= $3';
            v_inclusive_range_predicate := v_inclusive_range_predicate || ' AND o.name COLLATE "C" >= $3';
        END IF;
    END IF;

    -- Build batch query (dynamic SQL - called infrequently, amortized over many rows)
    -- The multi-row order matches the externally serialized cursor exactly:
    -- archived_at at millisecond precision, then version as the final tiebreak.
    --
    -- When v_multi_row, the seek is a keyset tuple comparison ("name > $2 OR
    -- (name = $2 AND tiebreak)") - Postgres won't split that OR into indexable
    -- form (confirmed even with fully literal values), so as one WHERE clause
    -- it forces a full bucket scan filtered row-by-row. Splitting it into two
    -- independently-indexable branches (exact name match with the tiebreak
    -- filter, vs. strictly-past names) combined with UNION ALL lets each
    -- branch keep name as a real index condition; the outer ORDER BY/LIMIT
    -- re-merges them into the same page the single query used to produce.
    IF v_multi_row THEN
        v_batch_query := format(
            $sql$
            SELECT *
            FROM (
                (
                    SELECT o.name, o.id, o.updated_at, o.created_at,
                           o.last_accessed_at, o.metadata, o.version,
                           o.archived_at, o.is_delete_marker, o.is_versioned
                    FROM storage.objects o
                    WHERE o.bucket_id = $1
                      AND o.name COLLATE "C" = $2
                      AND %s
                      AND NOT $7::boolean
                      AND (
                          $5::timestamptz IS NULL
                          OR COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) < $5
                          OR (
                              COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) = $5
                              AND COALESCE(o.version, '') > $6
                          )
                      )
                      %s
                    ORDER BY
                        COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) DESC,
                        COALESCE(o.version, '') ASC
                    LIMIT $4
                )
                UNION ALL
                (
                    SELECT o.name, o.id, o.updated_at, o.created_at,
                           o.last_accessed_at, o.metadata, o.version,
                           o.archived_at, o.is_delete_marker, o.is_versioned
                    FROM storage.objects o
                    WHERE o.bucket_id = $1
                      AND %s
                      %s
                    ORDER BY
                        o.name COLLATE "C" %s,
                        COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) DESC,
                        COALESCE(o.version, '') ASC
                    LIMIT $4
                )
            ) sub
            ORDER BY
                sub.name COLLATE "C" %s,
                COALESCE(date_trunc('milliseconds', sub.archived_at), 'infinity'::timestamptz) DESC,
                COALESCE(sub.version, '') ASC
            LIMIT $4
            $sql$,
            v_exact_range_predicate,
            v_version_filter,
            v_strict_range_predicate,
            v_version_filter,
            v_name_order,
            v_name_order
        );
    ELSE
        v_batch_query := format(
            $sql$
            SELECT o.name, o.id, o.updated_at, o.created_at,
                   o.last_accessed_at, o.metadata, o.version,
                   o.archived_at, o.is_delete_marker, o.is_versioned
            FROM storage.objects o
            WHERE o.bucket_id = $1
              AND %s
              %s
            ORDER BY o.name COLLATE "C" %s, o.archived_at DESC
            LIMIT $4
            $sql$,
            v_inclusive_range_predicate,
            v_version_filter,
            v_name_order
        );

        -- Strict counterpart of the query above: used once the single-row
        -- ASC batch advance (below) has left v_next_seek pointing at the
        -- last row already emitted, so an inclusive predicate would
        -- re-match it forever. Only single-row mode ever sets strict mode,
        -- so this variant is never needed when v_multi_row.
        v_batch_query_strict := format(
            $sql$
            SELECT o.name, o.id, o.updated_at, o.created_at,
                   o.last_accessed_at, o.metadata, o.version,
                   o.archived_at, o.is_delete_marker, o.is_versioned
            FROM storage.objects o
            WHERE o.bucket_id = $1
              AND %s
              %s
            ORDER BY o.name COLLATE "C" %s, o.archived_at DESC
            LIMIT $4
            $sql$,
            v_strict_range_predicate,
            v_version_filter,
            v_name_order
        );
    END IF;

    -- The static peek predicates cannot use the partial delete-marker index
    -- once PL/pgSQL switches to a generic plan because whether
    -- is_delete_marker is required remains parameter-dependent. Reuse the
    -- already-specialized batch query with a one-row limit for this sparse
    -- filter so the plan sees a literal `o.is_delete_marker` predicate.
    IF delete_markers = 'only' THEN
        v_delete_marker_peek_query :=
            'SELECT marker_page.name FROM (' || v_batch_query || ') marker_page LIMIT 1';
        IF NOT v_multi_row THEN
            v_delete_marker_peek_query_strict :=
                'SELECT marker_page.name FROM (' || v_batch_query_strict || ') marker_page LIMIT 1';
        END IF;
    END IF;

    -- ========================================================================
    -- SEEK INITIALIZATION: Determine starting position
    -- ========================================================================
    IF v_start = '' THEN
        IF v_is_asc THEN
            v_next_seek := v_prefix;
        ELSE
            -- DESC without cursor performs one specialized initial seek so
            -- partial current-version and delete-marker indexes remain available.
            EXECUTE format(
                'SELECT o.name FROM storage.objects o WHERE o.bucket_id = $1%s%s ORDER BY o.name COLLATE "C" DESC LIMIT 1',
                CASE WHEN v_upper_bound IS NOT NULL
                    THEN ' AND o.name COLLATE "C" >= $2 AND o.name COLLATE "C" < $3'
                    ELSE ''
                END,
                v_version_filter
            )
            INTO v_next_seek
            USING _bucket_id, v_prefix, v_upper_bound;

            IF v_next_seek IS NOT NULL THEN
                v_next_seek := v_next_seek || delimiter_param;
            ELSE
                RETURN;
            END IF;
        END IF;
    ELSE
        -- Folder continuation tokens retain their trailing delimiter. A
        -- delimiter-less startAfter is always a literal key boundary.
        v_cursor_is_folder := delimiter_param <> ''
            AND v_start_relative <> ''
            AND right(v_start_relative, length(delimiter_param)) = delimiter_param;

        IF v_cursor_is_folder THEN
            v_next_seek := CASE
                WHEN right(v_start, length(delimiter_param)) = delimiter_param
                    THEN v_start
                ELSE v_start || delimiter_param
            END;
            IF v_is_asc THEN
                v_next_seek := left(v_next_seek, -1)
                    || chr(ascii(right(v_next_seek, 1)) + 1);
            END IF;
            v_next_seek_strict := NOT v_is_asc;
        ELSE
            -- leaf object: when v_multi_row, stay on v_start with the
            -- caller-supplied tiebreak so a page boundary mid-key resumes
            -- that key's remaining rows instead of skipping them. Truncate
            -- to milliseconds like every other v_next_seek_at assignment -
            -- harmless today since object.ts's cursor always round-trips
            -- through JS Date first, but this shouldn't rely on that.
            IF v_multi_row THEN
                v_next_seek := v_start;
                v_next_seek_at := date_trunc('milliseconds', next_token_archived_at);
                v_next_seek_version := coalesce(next_token_version, '');
                v_next_seek_strict := coalesce(next_token, '') = '';
            ELSIF v_is_asc THEN
                v_next_seek := v_start;
                v_next_seek_strict := true;
            ELSE
                v_next_seek := v_start;
            END IF;
        END IF;
    END IF;

    -- ========================================================================
    -- MAIN LOOP: Hybrid peek-then-batch algorithm
    -- Uses STATIC SQL for peek (hot path) and DYNAMIC SQL for batch
    -- ========================================================================
    LOOP
        EXIT WHEN v_count >= max_keys;

        v_previous_seek := v_next_seek;
        v_previous_seek_at := v_next_seek_at;
        v_previous_seek_version := v_next_seek_version;
        v_previous_count := v_count;

        -- STEP 1: PEEK using STATIC SQL (plan cached, very fast)
        -- v_multi_row is branched here (rather than folded into the WHERE
        -- clause as a bound parameter) so each concrete query keeps an
        -- unconditional seek predicate - once PL/pgSQL switches to its
        -- cached generic plan (after 5 calls), a parameter-gated
        -- "(NOT v_multi_row AND name >= $x) OR (v_multi_row AND ...)"
        -- predicate stops the planner from using name as an index
        -- condition at all, degrading every subsequent peek to a full
        -- index scan filtered row-by-row instead of a bounded range scan.
        -- v_multi_row's seek predicate is a keyset tuple comparison
        -- ("name > x OR (name = x AND tiebreak)") - Postgres does not
        -- split this OR into indexable form even with fully literal
        -- values, so it falls back to a full scan filtered row-by-row.
        -- Splitting it into two independently-indexable branches (exact
        -- name match with the tiebreak filter, vs. strictly-past name)
        -- combined with UNION ALL lets each branch keep name as a real
        -- index condition; the outer ORDER BY/LIMIT picks whichever of
        -- the (at most 2) rows sorts first.
        IF delete_markers = 'only' THEN
            EXECUTE CASE WHEN v_next_seek_strict AND NOT v_multi_row
                THEN v_delete_marker_peek_query_strict
                ELSE v_delete_marker_peek_query
            END
                INTO v_peek_name
                USING _bucket_id, v_next_seek,
                    CASE WHEN v_is_asc THEN COALESCE(v_upper_bound, v_prefix) ELSE v_prefix END,
                    1, v_next_seek_at, v_next_seek_version, v_next_seek_strict;
        ELSIF v_multi_row THEN
            IF v_is_asc THEN
                IF v_upper_bound IS NOT NULL THEN
                    SELECT sub.name INTO v_peek_name FROM (
                        (SELECT o.name FROM storage.objects o
                         WHERE o.bucket_id = _bucket_id AND o.name COLLATE "C" = v_next_seek
                           AND o.name COLLATE "C" < v_upper_bound
                           AND NOT v_next_seek_strict
                           AND (v_next_seek_at IS NULL
                                OR COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) < v_next_seek_at
                                OR (COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) = v_next_seek_at
                                    AND COALESCE(o.version, '') > v_next_seek_version))
                           AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                           AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                           AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                           AND (delete_markers != 'only' OR o.is_delete_marker)
                         ORDER BY COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) DESC, COALESCE(o.version, '') ASC LIMIT 1)
                        UNION ALL
                        (SELECT o.name FROM storage.objects o
                         WHERE o.bucket_id = _bucket_id AND o.name COLLATE "C" > v_next_seek AND o.name COLLATE "C" < v_upper_bound
                           AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                           AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                           AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                           AND (delete_markers != 'only' OR o.is_delete_marker)
                         ORDER BY o.name COLLATE "C" ASC LIMIT 1)
                    ) sub ORDER BY sub.name COLLATE "C" ASC LIMIT 1;
                ELSE
                    SELECT sub.name INTO v_peek_name FROM (
                        (SELECT o.name FROM storage.objects o
                         WHERE o.bucket_id = _bucket_id AND o.name COLLATE "C" = v_next_seek
                           AND NOT v_next_seek_strict
                           AND (v_next_seek_at IS NULL
                                OR COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) < v_next_seek_at
                                OR (COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) = v_next_seek_at
                                    AND COALESCE(o.version, '') > v_next_seek_version))
                           AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                           AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                           AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                           AND (delete_markers != 'only' OR o.is_delete_marker)
                         ORDER BY COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) DESC, COALESCE(o.version, '') ASC LIMIT 1)
                        UNION ALL
                        (SELECT o.name FROM storage.objects o
                         WHERE o.bucket_id = _bucket_id AND o.name COLLATE "C" > v_next_seek
                           AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                           AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                           AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                           AND (delete_markers != 'only' OR o.is_delete_marker)
                         ORDER BY o.name COLLATE "C" ASC LIMIT 1)
                    ) sub ORDER BY sub.name COLLATE "C" ASC LIMIT 1;
                END IF;
            ELSE
                IF v_upper_bound IS NOT NULL THEN
                    SELECT sub.name INTO v_peek_name FROM (
                        (SELECT o.name FROM storage.objects o
                         WHERE o.bucket_id = _bucket_id AND o.name COLLATE "C" = v_next_seek
                           AND o.name COLLATE "C" >= v_prefix
                           AND NOT v_next_seek_strict
                           AND (v_next_seek_at IS NULL
                                OR COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) < v_next_seek_at
                                OR (COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) = v_next_seek_at
                                    AND COALESCE(o.version, '') > v_next_seek_version))
                           AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                           AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                           AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                           AND (delete_markers != 'only' OR o.is_delete_marker)
                         ORDER BY COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) DESC, COALESCE(o.version, '') ASC LIMIT 1)
                        UNION ALL
                        (SELECT o.name FROM storage.objects o
                         WHERE o.bucket_id = _bucket_id AND o.name COLLATE "C" < v_next_seek AND o.name COLLATE "C" >= v_prefix
                           AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                           AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                           AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                           AND (delete_markers != 'only' OR o.is_delete_marker)
                         ORDER BY o.name COLLATE "C" DESC LIMIT 1)
                    ) sub ORDER BY sub.name COLLATE "C" DESC LIMIT 1;
                ELSE
                    SELECT sub.name INTO v_peek_name FROM (
                        (SELECT o.name FROM storage.objects o
                         WHERE o.bucket_id = _bucket_id AND o.name COLLATE "C" = v_next_seek
                           AND NOT v_next_seek_strict
                           AND (v_next_seek_at IS NULL
                                OR COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) < v_next_seek_at
                                OR (COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) = v_next_seek_at
                                    AND COALESCE(o.version, '') > v_next_seek_version))
                           AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                           AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                           AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                           AND (delete_markers != 'only' OR o.is_delete_marker)
                         ORDER BY COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) DESC, COALESCE(o.version, '') ASC LIMIT 1)
                        UNION ALL
                        (SELECT o.name FROM storage.objects o
                         WHERE o.bucket_id = _bucket_id AND o.name COLLATE "C" < v_next_seek
                           AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                           AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                           AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                           AND (delete_markers != 'only' OR o.is_delete_marker)
                         ORDER BY o.name COLLATE "C" DESC LIMIT 1)
                    ) sub ORDER BY sub.name COLLATE "C" DESC LIMIT 1;
                END IF;
            END IF;
        ELSE
            -- Single-row mode is always noncurrent_versions='exclude'. Keep
            -- this predicate literal so generic plans use the current index.
            IF v_is_asc THEN
                IF v_next_seek_strict AND v_upper_bound IS NOT NULL THEN
                    SELECT o.name INTO v_peek_name FROM storage.objects o
                    WHERE o.bucket_id = _bucket_id
                      AND o.name COLLATE "C" > v_next_seek
                      AND o.name COLLATE "C" < v_upper_bound
                      AND o.archived_at IS NULL
                      AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                      AND (delete_markers != 'only' OR o.is_delete_marker)
                    ORDER BY o.name COLLATE "C" ASC LIMIT 1;
                ELSIF v_next_seek_strict THEN
                    SELECT o.name INTO v_peek_name FROM storage.objects o
                    WHERE o.bucket_id = _bucket_id
                      AND o.name COLLATE "C" > v_next_seek
                      AND o.archived_at IS NULL
                      AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                      AND (delete_markers != 'only' OR o.is_delete_marker)
                    ORDER BY o.name COLLATE "C" ASC LIMIT 1;
                ELSIF v_upper_bound IS NOT NULL THEN
                    SELECT o.name INTO v_peek_name FROM storage.objects o
                    WHERE o.bucket_id = _bucket_id
                      AND o.name COLLATE "C" >= v_next_seek
                      AND o.name COLLATE "C" < v_upper_bound
                      AND o.archived_at IS NULL
                      AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                      AND (delete_markers != 'only' OR o.is_delete_marker)
                    ORDER BY o.name COLLATE "C" ASC LIMIT 1;
                ELSE
                    SELECT o.name INTO v_peek_name FROM storage.objects o
                    WHERE o.bucket_id = _bucket_id
                      AND o.name COLLATE "C" >= v_next_seek
                      AND o.archived_at IS NULL
                      AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                      AND (delete_markers != 'only' OR o.is_delete_marker)
                    ORDER BY o.name COLLATE "C" ASC LIMIT 1;
                END IF;
            ELSE
                IF v_upper_bound IS NOT NULL THEN
                    SELECT o.name INTO v_peek_name FROM storage.objects o
                    WHERE o.bucket_id = _bucket_id
                      AND o.name COLLATE "C" < v_next_seek
                      AND o.name COLLATE "C" >= v_prefix
                      AND o.archived_at IS NULL
                      AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                      AND (delete_markers != 'only' OR o.is_delete_marker)
                    ORDER BY o.name COLLATE "C" DESC LIMIT 1;
                ELSE
                    SELECT o.name INTO v_peek_name FROM storage.objects o
                    WHERE o.bucket_id = _bucket_id
                      AND o.name COLLATE "C" < v_next_seek
                      AND o.archived_at IS NULL
                      AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                      AND (delete_markers != 'only' OR o.is_delete_marker)
                    ORDER BY o.name COLLATE "C" DESC LIMIT 1;
                END IF;
            END IF;
        END IF;

        EXIT WHEN v_peek_name IS NULL;

        -- STEP 2: Check if this is a FOLDER or FILE
        v_common_prefix := storage.get_common_prefix(v_peek_name, v_prefix, delimiter_param);

        IF v_common_prefix IS NOT NULL THEN
            -- FOLDER: Emit and skip to next folder (no heap access needed)
            name := v_common_prefix;
            id := NULL;
            updated_at := NULL;
            created_at := NULL;
            last_accessed_at := NULL;
            metadata := NULL;
            version := NULL;
            archived_at := NULL;
            is_delete_marker := NULL;
            is_versioned := NULL;
            RETURN NEXT;
            v_count := v_count + 1;

            -- Advance seek past the folder range
            IF v_is_asc THEN
                v_next_seek := left(v_common_prefix, -1)
                    || chr(ascii(right(v_common_prefix, 1)) + 1);
            ELSE
                v_next_seek := v_common_prefix;
            END IF;
            v_next_seek_at := NULL;
            v_next_seek_version := '';
            v_next_seek_strict := NOT v_is_asc;
        ELSE
            -- FILE: Batch fetch using DYNAMIC SQL (overhead amortized over many rows)
            -- For ASC: upper_bound is the exclusive upper limit (< condition)
            -- For DESC: prefix is the inclusive lower limit (>= condition)
            FOR v_current IN EXECUTE CASE WHEN v_next_seek_strict AND NOT v_multi_row THEN v_batch_query_strict ELSE v_batch_query END
                USING _bucket_id, v_next_seek,
                CASE WHEN v_is_asc THEN COALESCE(v_upper_bound, v_prefix) ELSE v_prefix END, v_file_batch_size, v_next_seek_at, v_next_seek_version,
                v_next_seek_strict
            LOOP
                v_common_prefix := storage.get_common_prefix(v_current.name, v_prefix, delimiter_param);

                IF v_common_prefix IS NOT NULL THEN
                    -- Hit a folder: exit batch, let peek handle it. Reset
                    -- strict mode too it may have been set by an earlier
                    -- row in this same batch (see the single-row ASC advance
                    -- below), and v_next_seek here is the folder-triggering
                    -- row's own name, which the next peek must find inclusively.
                    v_next_seek := CASE
                        WHEN v_is_asc THEN v_current.name
                        ELSE v_current.name || delimiter_param
                    END;
                    v_next_seek_at := NULL;
                    v_next_seek_version := '';
                    v_next_seek_strict := false;
                    EXIT;
                END IF;

                -- Emit file
                name := v_current.name;
                id := v_current.id;
                updated_at := v_current.updated_at;
                created_at := v_current.created_at;
                last_accessed_at := v_current.last_accessed_at;
                metadata := v_current.metadata;
                version := v_current.version;
                archived_at := v_current.archived_at;
                is_delete_marker := v_current.is_delete_marker;
                is_versioned := v_current.is_versioned;
                RETURN NEXT;
                v_count := v_count + 1;

                -- when v_multi_row, stay on this name and record its
                -- archived_at as the new tiebreak so remaining rows for the
                -- same key are picked up before moving to the next name
                IF v_multi_row THEN
                    v_next_seek := v_current.name;
                    v_next_seek_at := COALESCE(date_trunc('milliseconds', v_current.archived_at), 'infinity'::timestamptz);
                    v_next_seek_version := COALESCE(v_current.version, '');
                    v_next_seek_strict := false;
                ELSIF v_is_asc THEN
                    -- Appending the delimiter as a fake lexical successor
                    -- would skip a real key like `name || '!'` (or any
                    -- character sorting below the delimiter), which sorts
                    -- between `name` and `name || delimiter`. Track the real
                    -- name and mark the next comparison strict instead.
                    v_next_seek := v_current.name;
                    v_next_seek_strict := true;
                ELSE
                    v_next_seek := v_current.name;
                END IF;

                EXIT WHEN v_count >= max_keys;
            END LOOP;
        END IF;

        IF v_count = v_previous_count
           AND v_next_seek IS NOT DISTINCT FROM v_previous_seek
           AND v_next_seek_at IS NOT DISTINCT FROM v_previous_seek_at
           AND v_next_seek_version IS NOT DISTINCT FROM v_previous_seek_version THEN
            RAISE EXCEPTION 'storage.list_objects_with_delimiter made no progress at seek (%, %, %)',
                v_next_seek, v_next_seek_at, v_next_seek_version;
        END IF;
    END LOOP;
END;
$_$;


ALTER FUNCTION storage.list_objects_with_delimiter(_bucket_id text, prefix_param text, delimiter_param text, max_keys integer, start_after text, next_token text, sort_order text, noncurrent_versions text, delete_markers text, next_token_archived_at timestamp with time zone, next_token_version text) OWNER TO supabase_storage_admin;

--
-- Name: operation(); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.operation() RETURNS text
    LANGUAGE plpgsql STABLE
    AS $$
BEGIN
    RETURN current_setting('storage.operation', true);
END;
$$;


ALTER FUNCTION storage.operation() OWNER TO supabase_storage_admin;

--
-- Name: protect_bucket_control_columns(); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.protect_bucket_control_columns() RETURNS trigger
    LANGUAGE plpgsql
    SET search_path TO 'pg_catalog'
    AS $$
DECLARE
  configuration_changed boolean;
BEGIN
  IF TG_OP = 'INSERT' THEN
    IF NEW.lifecycle_configuration IS NOT NULL
       OR NEW.lifecycle_configuration_generation IS NOT NULL THEN
      IF NOT pg_has_role(current_user, TG_ARGV[0], 'MEMBER') THEN
        RAISE EXCEPTION 'only members of the configured storage service role may insert lifecycle policy state'
          USING ERRCODE = '42501',
                HINT = format(
                  'Insert with both lifecycle columns NULL and configure lifecycle through the Storage API afterward, or insert as a member of %I.',
                  TG_ARGV[0]
                );
      END IF;
    END IF;

    RETURN NEW;
  END IF;

  configuration_changed =
    OLD.lifecycle_configuration IS DISTINCT FROM NEW.lifecycle_configuration
    OR OLD.lifecycle_configuration_generation IS DISTINCT FROM NEW.lifecycle_configuration_generation;

  IF NOT configuration_changed THEN
    RETURN NEW;
  END IF;

  IF NEW.type IS DISTINCT FROM 'STANDARD' THEN
    RAISE EXCEPTION 'bucket versioning and lifecycle controls require a Standard bucket'
      USING ERRCODE = '0A000';
  END IF;

  IF NEW.lifecycle_configuration IS NULL
     AND NEW.lifecycle_configuration_generation IS NULL THEN
    RETURN NEW;
  END IF;

  IF NEW.lifecycle_configuration IS NULL
     OR NEW.lifecycle_configuration_generation IS NULL
     OR OLD.lifecycle_configuration IS NOT DISTINCT FROM NEW.lifecycle_configuration
     OR OLD.lifecycle_configuration_generation IS NOT DISTINCT FROM NEW.lifecycle_configuration_generation THEN
    RAISE EXCEPTION 'a changed lifecycle policy requires a new non-null generation'
      USING ERRCODE = '22023';
  END IF;

  RETURN NEW;
END;
$$;


ALTER FUNCTION storage.protect_bucket_control_columns() OWNER TO supabase_storage_admin;

--
-- Name: protect_delete(); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.protect_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    -- Check if storage.allow_delete_query is set to 'true'
    IF COALESCE(current_setting('storage.allow_delete_query', true), 'false') != 'true' THEN
        RAISE EXCEPTION 'Direct deletion from storage tables is not allowed. Use the Storage API instead.'
            USING HINT = 'This prevents accidental data loss from orphaned objects.',
                  ERRCODE = '42501';
    END IF;
    RETURN NULL;
END;
$$;


ALTER FUNCTION storage.protect_delete() OWNER TO supabase_storage_admin;

--
-- Name: search(text, text, integer, integer, integer, text, text, text, text, text); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.search(prefix text, bucketname text, limits integer DEFAULT 100, levels integer DEFAULT 1, offsets integer DEFAULT 0, search text DEFAULT ''::text, sortcolumn text DEFAULT 'name'::text, sortorder text DEFAULT 'asc'::text, noncurrent_versions text DEFAULT 'exclude'::text, delete_markers text DEFAULT 'exclude'::text) RETURNS TABLE(name text, id uuid, updated_at timestamp with time zone, created_at timestamp with time zone, last_accessed_at timestamp with time zone, metadata jsonb, version text, archived_at timestamp with time zone, is_delete_marker boolean, is_versioned boolean)
    LANGUAGE plpgsql STABLE
    AS $_$
DECLARE
    v_peek_name TEXT;
    v_current RECORD;
    v_common_prefix TEXT;
    v_delimiter CONSTANT TEXT := '/';

    -- Configuration
    v_limit INT;
    v_prefix TEXT;
    v_prefix_lower TEXT;
    v_prefix_len INT;
    v_prefix_start INT;
    v_combined_levels INT;
    v_is_asc BOOLEAN;
    v_order_by TEXT;
    v_sort_order TEXT;
    v_upper_bound TEXT;
    v_file_batch_size INT;
    v_version_filter TEXT;
    v_multi_row BOOLEAN;

    -- Dynamic SQL for batch query only
    v_batch_query TEXT;
    v_delete_marker_peek_query TEXT;
    v_delete_marker_peek_query_strict TEXT;

    -- Seek state
    v_next_seek TEXT;
    v_next_seek_at TIMESTAMPTZ;
    v_next_seek_version TEXT;
    v_next_seek_strict BOOLEAN := false;
    v_count INT := 0;
    v_skipped INT := 0;
    v_previous_seek TEXT;
    v_previous_seek_at TIMESTAMPTZ;
    v_previous_seek_version TEXT;
    v_previous_count INT;
    v_previous_skipped INT;
BEGIN
    -- ========================================================================
    -- INITIALIZATION
    -- ========================================================================
    v_limit := LEAST(coalesce(limits, 100), 1500);
    v_prefix := coalesce(prefix, '') || coalesce(search, '');
    v_prefix_lower := lower(v_prefix);
    v_prefix_len := length(coalesce(prefix, ''));
    v_prefix_start := coalesce(array_length(string_to_array(coalesce(prefix, ''), v_delimiter), 1), 1);
    v_combined_levels := coalesce(array_length(string_to_array(v_prefix, v_delimiter), 1), 1);
    v_is_asc := lower(coalesce(sortorder, 'asc')) = 'asc';
    v_file_batch_size := LEAST(GREATEST(v_limit * 2, 100), 1000);
    v_next_seek_at := NULL;
    v_next_seek_version := '';

    -- COALESCE first: NULL NOT IN (...) evaluates to NULL (not TRUE), so a
    -- bare NOT IN check silently leaves an explicit NULL argument unreset.
    noncurrent_versions := COALESCE(noncurrent_versions, 'exclude');
    delete_markers := COALESCE(delete_markers, 'exclude');
    IF noncurrent_versions NOT IN ('exclude', 'only', 'include') THEN
        noncurrent_versions := 'exclude';
    END IF;
    IF delete_markers NOT IN ('exclude', 'only', 'include') THEN
        delete_markers := 'exclude';
    END IF;

    v_multi_row := noncurrent_versions IN ('only', 'include');

    v_version_filter := '';
    IF noncurrent_versions = 'exclude' THEN
        v_version_filter := v_version_filter || ' AND o.archived_at IS NULL';
    ELSIF noncurrent_versions = 'only' THEN
        v_version_filter := v_version_filter || ' AND o.archived_at IS NOT NULL';
    END IF;
    IF delete_markers = 'exclude' THEN
        v_version_filter := v_version_filter || ' AND NOT o.is_delete_marker';
    ELSIF delete_markers = 'only' THEN
        v_version_filter := v_version_filter || ' AND o.is_delete_marker';
    END IF;

    -- Validate sort column
    CASE lower(coalesce(sortcolumn, 'name'))
        WHEN 'name' THEN v_order_by := 'name';
        WHEN 'updated_at' THEN v_order_by := 'updated_at';
        WHEN 'created_at' THEN v_order_by := 'created_at';
        WHEN 'last_accessed_at' THEN v_order_by := 'last_accessed_at';
        ELSE v_order_by := 'name';
    END CASE;

    v_sort_order := CASE WHEN v_is_asc THEN 'asc' ELSE 'desc' END;

    -- ========================================================================
    -- NON-NAME SORTING: Use path_tokens approach
    -- ========================================================================
    IF v_order_by != 'name' THEN
        RETURN QUERY EXECUTE format(
            $sql$
            WITH folders AS (
                SELECT array_to_string(path_tokens[$1:$2], '/') AS folder
                FROM storage.objects
                WHERE objects.name ILIKE $3 || '%%'
                  AND bucket_id = $4
                  AND array_length(objects.path_tokens, 1) <> $2
                  AND ($7 != 'exclude' OR objects.archived_at IS NULL)
                  AND ($7 != 'only' OR objects.archived_at IS NOT NULL)
                  AND ($8 != 'exclude' OR NOT objects.is_delete_marker)
                  AND ($8 != 'only' OR objects.is_delete_marker)
                GROUP BY folder
                ORDER BY folder %s
            )
            (SELECT folder AS "name",
                   NULL::uuid AS id,
                   NULL::timestamptz AS updated_at,
                   NULL::timestamptz AS created_at,
                   NULL::timestamptz AS last_accessed_at,
                   NULL::jsonb AS metadata,
                   NULL::text AS version,
                   NULL::timestamptz AS archived_at,
                   NULL::boolean AS is_delete_marker,
                   NULL::boolean AS is_versioned FROM folders)
            UNION ALL
            (SELECT array_to_string(path_tokens[$1:$2], '/') AS "name",
                   id, updated_at, created_at, last_accessed_at, metadata,
                   version, archived_at, is_delete_marker, is_versioned
             FROM storage.objects
             WHERE objects.name ILIKE $3 || '%%'
               AND bucket_id = $4
               AND array_length(objects.path_tokens, 1) = $2
               AND ($7 != 'exclude' OR objects.archived_at IS NULL)
               AND ($7 != 'only' OR objects.archived_at IS NOT NULL)
               AND ($8 != 'exclude' OR NOT objects.is_delete_marker)
               AND ($8 != 'only' OR objects.is_delete_marker)
             -- name, then version, as tiebreaks so two versions of the same
             -- key tying on the sort column still sort deterministically
             ORDER BY %I %s, name COLLATE "C" %s, COALESCE(version, '') %s)
            LIMIT $5 OFFSET $6
            $sql$, v_sort_order, v_order_by, v_sort_order, v_sort_order, v_sort_order
        ) USING v_prefix_start, v_combined_levels, v_prefix, bucketname, v_limit, offsets, noncurrent_versions, delete_markers;
        RETURN;
    END IF;

    -- ========================================================================
    -- NAME SORTING: Hybrid skip-scan with batch optimization
    -- ========================================================================

    -- Calculate upper bound for prefix filtering
    IF v_prefix_lower = '' THEN
        v_upper_bound := NULL;
    ELSIF right(v_prefix_lower, 1) = v_delimiter THEN
        v_upper_bound := left(v_prefix_lower, -1) || chr(ascii(v_delimiter) + 1);
    ELSE
        v_upper_bound := left(v_prefix_lower, -1) || chr(ascii(right(v_prefix_lower, 1)) + 1);
    END IF;

    -- Build a resume-safe batch query. The exact-name branch returns remaining
    -- versions after the current (archived_at, version) boundary; the strict
    -- name branch returns subsequent keys. UNION ALL keeps both predicates
    -- independently indexable.
    IF v_is_asc THEN
        IF v_upper_bound IS NOT NULL THEN
            v_batch_query := 'SELECT * FROM (' ||
                '(SELECT o.name, o.id, o.updated_at, o.created_at, o.last_accessed_at, o.metadata, o.version, o.archived_at, o.is_delete_marker, o.is_versioned FROM storage.objects o ' ||
                'WHERE o.bucket_id = $1 AND lower(o.name) COLLATE "C" = $2 AND ($5::timestamptz IS NULL OR COALESCE(o.archived_at, ''infinity''::timestamptz) < $5 OR (COALESCE(o.archived_at, ''infinity''::timestamptz) = $5 AND COALESCE(o.version, '''') > $6))' ||
                v_version_filter || ' ORDER BY COALESCE(o.archived_at, ''infinity''::timestamptz) DESC, COALESCE(o.version, '''') ASC LIMIT $4) UNION ALL ' ||
                '(SELECT o.name, o.id, o.updated_at, o.created_at, o.last_accessed_at, o.metadata, o.version, o.archived_at, o.is_delete_marker, o.is_versioned FROM storage.objects o ' ||
                'WHERE o.bucket_id = $1 AND lower(o.name) COLLATE "C" > $2 AND lower(o.name) COLLATE "C" < $3' || v_version_filter ||
                ' ORDER BY lower(o.name) COLLATE "C" ASC, COALESCE(o.archived_at, ''infinity''::timestamptz) DESC, COALESCE(o.version, '''') ASC LIMIT $4)' ||
                ') sub ORDER BY lower(sub.name) COLLATE "C" ASC, COALESCE(sub.archived_at, ''infinity''::timestamptz) DESC, COALESCE(sub.version, '''') ASC LIMIT $4';
        ELSE
            v_batch_query := 'SELECT * FROM (' ||
                '(SELECT o.name, o.id, o.updated_at, o.created_at, o.last_accessed_at, o.metadata, o.version, o.archived_at, o.is_delete_marker, o.is_versioned FROM storage.objects o ' ||
                'WHERE o.bucket_id = $1 AND lower(o.name) COLLATE "C" = $2 AND ($5::timestamptz IS NULL OR COALESCE(o.archived_at, ''infinity''::timestamptz) < $5 OR (COALESCE(o.archived_at, ''infinity''::timestamptz) = $5 AND COALESCE(o.version, '''') > $6))' ||
                v_version_filter || ' ORDER BY COALESCE(o.archived_at, ''infinity''::timestamptz) DESC, COALESCE(o.version, '''') ASC LIMIT $4) UNION ALL ' ||
                '(SELECT o.name, o.id, o.updated_at, o.created_at, o.last_accessed_at, o.metadata, o.version, o.archived_at, o.is_delete_marker, o.is_versioned FROM storage.objects o ' ||
                'WHERE o.bucket_id = $1 AND lower(o.name) COLLATE "C" > $2' || v_version_filter ||
                ' ORDER BY lower(o.name) COLLATE "C" ASC, COALESCE(o.archived_at, ''infinity''::timestamptz) DESC, COALESCE(o.version, '''') ASC LIMIT $4)' ||
                ') sub ORDER BY lower(sub.name) COLLATE "C" ASC, COALESCE(sub.archived_at, ''infinity''::timestamptz) DESC, COALESCE(sub.version, '''') ASC LIMIT $4';
        END IF;
    ELSE
        IF v_upper_bound IS NOT NULL THEN
            v_batch_query := 'SELECT * FROM (' ||
                '(SELECT o.name, o.id, o.updated_at, o.created_at, o.last_accessed_at, o.metadata, o.version, o.archived_at, o.is_delete_marker, o.is_versioned FROM storage.objects o ' ||
                'WHERE o.bucket_id = $1 AND lower(o.name) COLLATE "C" = $2 AND ($5::timestamptz IS NULL OR COALESCE(o.archived_at, ''infinity''::timestamptz) < $5 OR (COALESCE(o.archived_at, ''infinity''::timestamptz) = $5 AND COALESCE(o.version, '''') > $6))' ||
                v_version_filter || ' ORDER BY COALESCE(o.archived_at, ''infinity''::timestamptz) DESC, COALESCE(o.version, '''') ASC LIMIT $4) UNION ALL ' ||
                '(SELECT o.name, o.id, o.updated_at, o.created_at, o.last_accessed_at, o.metadata, o.version, o.archived_at, o.is_delete_marker, o.is_versioned FROM storage.objects o ' ||
                'WHERE o.bucket_id = $1 AND lower(o.name) COLLATE "C" < $2 AND lower(o.name) COLLATE "C" >= $3' || v_version_filter ||
                ' ORDER BY lower(o.name) COLLATE "C" DESC, COALESCE(o.archived_at, ''infinity''::timestamptz) DESC, COALESCE(o.version, '''') ASC LIMIT $4)' ||
                ') sub ORDER BY lower(sub.name) COLLATE "C" DESC, COALESCE(sub.archived_at, ''infinity''::timestamptz) DESC, COALESCE(sub.version, '''') ASC LIMIT $4';
        ELSE
            v_batch_query := 'SELECT * FROM (' ||
                '(SELECT o.name, o.id, o.updated_at, o.created_at, o.last_accessed_at, o.metadata, o.version, o.archived_at, o.is_delete_marker, o.is_versioned FROM storage.objects o ' ||
                'WHERE o.bucket_id = $1 AND lower(o.name) COLLATE "C" = $2 AND ($5::timestamptz IS NULL OR COALESCE(o.archived_at, ''infinity''::timestamptz) < $5 OR (COALESCE(o.archived_at, ''infinity''::timestamptz) = $5 AND COALESCE(o.version, '''') > $6))' ||
                v_version_filter || ' ORDER BY COALESCE(o.archived_at, ''infinity''::timestamptz) DESC, COALESCE(o.version, '''') ASC LIMIT $4) UNION ALL ' ||
                '(SELECT o.name, o.id, o.updated_at, o.created_at, o.last_accessed_at, o.metadata, o.version, o.archived_at, o.is_delete_marker, o.is_versioned FROM storage.objects o ' ||
                'WHERE o.bucket_id = $1 AND lower(o.name) COLLATE "C" < $2' || v_version_filter ||
                ' ORDER BY lower(o.name) COLLATE "C" DESC, COALESCE(o.archived_at, ''infinity''::timestamptz) DESC, COALESCE(o.version, '''') ASC LIMIT $4)' ||
                ') sub ORDER BY lower(sub.name) COLLATE "C" DESC, COALESCE(sub.archived_at, ''infinity''::timestamptz) DESC, COALESCE(sub.version, '''') ASC LIMIT $4';
        END IF;
    END IF;

    -- Keep the delete-marker predicate literal so the cached generic
    -- plan can use idx_objects_delete_markers during the main-loop peek.
    IF delete_markers = 'only' THEN
        IF v_multi_row THEN
            v_delete_marker_peek_query :=
                'SELECT marker_page.name FROM (' || v_batch_query || ') marker_page LIMIT 1';
        ELSIF v_is_asc THEN
            -- Two separate literal query strings, not one gated by a bound
            -- boolean: folding "$n AND op1 OR NOT $n AND op2" into a single
            -- query defeats the generic plan's ability to push either
            -- comparison into the index. Branching in PL/pgSQL control flow
            -- instead keeps each query's index condition intact.
            v_delete_marker_peek_query :=
                'SELECT o.name FROM storage.objects o WHERE o.bucket_id = $1 ' ||
                'AND lower(o.name) COLLATE "C" >= $2' ||
                CASE WHEN v_upper_bound IS NOT NULL
                    THEN ' AND lower(o.name) COLLATE "C" < $3'
                    ELSE ''
                END ||
                v_version_filter ||
                ' ORDER BY lower(o.name) COLLATE "C" ASC LIMIT 1';
            -- Strict variant: used once the single-row ASC batch advance
            -- (below) has left v_next_seek pointing at the last row already
            -- emitted, so a plain >= would re-match it forever.
            v_delete_marker_peek_query_strict :=
                'SELECT o.name FROM storage.objects o WHERE o.bucket_id = $1 ' ||
                'AND lower(o.name) COLLATE "C" > $2' ||
                CASE WHEN v_upper_bound IS NOT NULL
                    THEN ' AND lower(o.name) COLLATE "C" < $3'
                    ELSE ''
                END ||
                v_version_filter ||
                ' ORDER BY lower(o.name) COLLATE "C" ASC LIMIT 1';
        ELSE
            v_delete_marker_peek_query :=
                'SELECT o.name FROM storage.objects o WHERE o.bucket_id = $1 ' ||
                'AND lower(o.name) COLLATE "C" < $2' ||
                CASE WHEN v_upper_bound IS NOT NULL
                    THEN ' AND lower(o.name) COLLATE "C" >= $3'
                    ELSE ''
                END ||
                v_version_filter ||
                ' ORDER BY lower(o.name) COLLATE "C" DESC LIMIT 1';
        END IF;
    END IF;

    -- Initialize seek position
    IF v_is_asc THEN
        v_next_seek := v_prefix_lower;
    ELSE
        -- DESC performs one specialized initial seek so partial current-version
        -- and delete-marker indexes remain available.
        EXECUTE format(
            'SELECT o.name FROM storage.objects o WHERE o.bucket_id = $1%s%s ORDER BY lower(o.name) COLLATE "C" DESC LIMIT 1',
            CASE WHEN v_upper_bound IS NOT NULL
                THEN ' AND lower(o.name) COLLATE "C" >= $2 AND lower(o.name) COLLATE "C" < $3'
                ELSE ''
            END,
            v_version_filter
        )
        INTO v_peek_name
        USING bucketname, v_prefix_lower, v_upper_bound;

        IF v_peek_name IS NOT NULL THEN
            v_next_seek := lower(v_peek_name) || v_delimiter;
        ELSE
            RETURN;
        END IF;
    END IF;

    -- ========================================================================
    -- MAIN LOOP: Hybrid peek-then-batch algorithm
    -- Uses STATIC SQL for peek (hot path) and DYNAMIC SQL for batch and
    -- the delete-marker-only path
    -- ========================================================================
    LOOP
        EXIT WHEN v_count >= v_limit;

        v_previous_seek := v_next_seek;
        v_previous_seek_at := v_next_seek_at;
        v_previous_seek_version := v_next_seek_version;
        v_previous_count := v_count;
        v_previous_skipped := v_skipped;

        -- STEP 1: PEEK
        v_peek_name := NULL;
        IF delete_markers = 'only' THEN
            EXECUTE CASE WHEN v_next_seek_strict
                THEN v_delete_marker_peek_query_strict
                ELSE v_delete_marker_peek_query
            END
                INTO v_peek_name
                USING bucketname, v_next_seek,
                    CASE WHEN v_is_asc THEN COALESCE(v_upper_bound, v_prefix_lower) ELSE v_prefix_lower END,
                    1, v_next_seek_at, v_next_seek_version;
        ELSIF v_multi_row AND v_next_seek_at IS NOT NULL THEN
            SELECT o.name INTO v_peek_name
            FROM storage.objects o
            WHERE o.bucket_id = bucketname
              AND lower(o.name) COLLATE "C" = v_next_seek
              AND (COALESCE(o.archived_at, 'infinity'::timestamptz) < v_next_seek_at
                   OR (COALESCE(o.archived_at, 'infinity'::timestamptz) = v_next_seek_at
                       AND COALESCE(o.version, '') > v_next_seek_version))
              AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
              AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
              AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
              AND (delete_markers != 'only' OR o.is_delete_marker)
            ORDER BY COALESCE(o.archived_at, 'infinity'::timestamptz) DESC,
                     COALESCE(o.version, '') ASC
            LIMIT 1;

            -- The current key is exhausted. Clear its version boundary and
            -- make the following ASC name peek strict. Appending '/' is not a
            -- valid lexical successor because keys ending in characters such
            -- as '!' sort between the exhausted name and name || '/'.
            IF v_peek_name IS NULL THEN
                IF v_is_asc THEN
                    v_next_seek_strict := true;
                END IF;
                v_next_seek_at := NULL;
                v_next_seek_version := '';
            END IF;
        END IF;

        -- Single-row mode is always noncurrent_versions='exclude'. Keep the
        -- current-row predicate literal so generic plans use the current index.
        IF delete_markers != 'only' AND v_peek_name IS NULL AND NOT v_multi_row THEN
            IF v_is_asc THEN
                IF v_next_seek_strict AND v_upper_bound IS NOT NULL THEN
                    SELECT o.name INTO v_peek_name FROM storage.objects o
                    WHERE o.bucket_id = bucketname AND lower(o.name) COLLATE "C" > v_next_seek AND lower(o.name) COLLATE "C" < v_upper_bound
                      AND o.archived_at IS NULL
                      AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                    ORDER BY lower(o.name) COLLATE "C" ASC LIMIT 1;
                ELSIF v_next_seek_strict THEN
                    SELECT o.name INTO v_peek_name FROM storage.objects o
                    WHERE o.bucket_id = bucketname AND lower(o.name) COLLATE "C" > v_next_seek
                      AND o.archived_at IS NULL
                      AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                    ORDER BY lower(o.name) COLLATE "C" ASC LIMIT 1;
                ELSIF v_upper_bound IS NOT NULL THEN
                    SELECT o.name INTO v_peek_name FROM storage.objects o
                    WHERE o.bucket_id = bucketname AND lower(o.name) COLLATE "C" >= v_next_seek AND lower(o.name) COLLATE "C" < v_upper_bound
                      AND o.archived_at IS NULL
                      AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                    ORDER BY lower(o.name) COLLATE "C" ASC LIMIT 1;
                ELSE
                    SELECT o.name INTO v_peek_name FROM storage.objects o
                    WHERE o.bucket_id = bucketname AND lower(o.name) COLLATE "C" >= v_next_seek
                      AND o.archived_at IS NULL
                      AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                    ORDER BY lower(o.name) COLLATE "C" ASC LIMIT 1;
                END IF;
            ELSIF v_upper_bound IS NOT NULL THEN
                SELECT o.name INTO v_peek_name FROM storage.objects o
                WHERE o.bucket_id = bucketname AND lower(o.name) COLLATE "C" < v_next_seek AND lower(o.name) COLLATE "C" >= v_prefix_lower
                  AND o.archived_at IS NULL
                  AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                ORDER BY lower(o.name) COLLATE "C" DESC LIMIT 1;
            ELSE
                SELECT o.name INTO v_peek_name FROM storage.objects o
                WHERE o.bucket_id = bucketname AND lower(o.name) COLLATE "C" < v_next_seek
                  AND o.archived_at IS NULL
                  AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                ORDER BY lower(o.name) COLLATE "C" DESC LIMIT 1;
            END IF;
        ELSIF delete_markers != 'only' AND v_peek_name IS NULL AND v_is_asc THEN
            IF v_next_seek_strict AND v_upper_bound IS NOT NULL THEN
                SELECT o.name INTO v_peek_name FROM storage.objects o
                WHERE o.bucket_id = bucketname AND lower(o.name) COLLATE "C" > v_next_seek AND lower(o.name) COLLATE "C" < v_upper_bound
                  AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                  AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                  AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                  AND (delete_markers != 'only' OR o.is_delete_marker)
                ORDER BY lower(o.name) COLLATE "C" ASC LIMIT 1;
            ELSIF v_next_seek_strict THEN
                SELECT o.name INTO v_peek_name FROM storage.objects o
                WHERE o.bucket_id = bucketname AND lower(o.name) COLLATE "C" > v_next_seek
                  AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                  AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                  AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                  AND (delete_markers != 'only' OR o.is_delete_marker)
                ORDER BY lower(o.name) COLLATE "C" ASC LIMIT 1;
            ELSIF v_upper_bound IS NOT NULL THEN
                SELECT o.name INTO v_peek_name FROM storage.objects o
                WHERE o.bucket_id = bucketname AND lower(o.name) COLLATE "C" >= v_next_seek AND lower(o.name) COLLATE "C" < v_upper_bound
                  AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                  AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                  AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                  AND (delete_markers != 'only' OR o.is_delete_marker)
                ORDER BY lower(o.name) COLLATE "C" ASC LIMIT 1;
            ELSE
                SELECT o.name INTO v_peek_name FROM storage.objects o
                WHERE o.bucket_id = bucketname AND lower(o.name) COLLATE "C" >= v_next_seek
                  AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                  AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                  AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                  AND (delete_markers != 'only' OR o.is_delete_marker)
                ORDER BY lower(o.name) COLLATE "C" ASC LIMIT 1;
            END IF;
        ELSIF delete_markers != 'only' AND v_peek_name IS NULL THEN
            IF v_upper_bound IS NOT NULL THEN
                SELECT o.name INTO v_peek_name FROM storage.objects o
                WHERE o.bucket_id = bucketname AND lower(o.name) COLLATE "C" < v_next_seek AND lower(o.name) COLLATE "C" >= v_prefix_lower
                  AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                  AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                  AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                  AND (delete_markers != 'only' OR o.is_delete_marker)
                ORDER BY lower(o.name) COLLATE "C" DESC LIMIT 1;
            ELSE
                SELECT o.name INTO v_peek_name FROM storage.objects o
                WHERE o.bucket_id = bucketname AND lower(o.name) COLLATE "C" < v_next_seek
                  AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                  AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                  AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                  AND (delete_markers != 'only' OR o.is_delete_marker)
                ORDER BY lower(o.name) COLLATE "C" DESC LIMIT 1;
            END IF;
        END IF;

        EXIT WHEN v_peek_name IS NULL;

        -- If the peek landed on a different key than we were tracking, any
        -- version boundary belongs to the OLD key and must not leak into the
        -- new one - e.g. the deleteMarkers='only' peek doesn't know or care
        -- whether it's continuing the same key or jumping to a new one, so
        -- it never clears these itself.
        IF lower(v_peek_name) IS DISTINCT FROM v_next_seek THEN
            v_next_seek_at := NULL;
            v_next_seek_version := '';
        END IF;

        -- The peek is authoritative for the next key to process. This is
        -- especially important after exhausting a multi-version key: the
        -- version boundary has been cleared, so executing the batch against
        -- a stale v_next_seek would replay every version of that old key.
        v_next_seek := lower(v_peek_name);
        v_next_seek_strict := false;

        -- STEP 2: Check if this is a FOLDER or FILE
        v_common_prefix := storage.get_common_prefix(lower(v_peek_name), v_prefix_lower, v_delimiter);

        IF v_common_prefix IS NOT NULL THEN
            -- FOLDER: Handle offset, emit if needed, skip to next folder
            IF v_skipped < offsets THEN
                v_skipped := v_skipped + 1;
            ELSE
                name := substring(rtrim(storage.get_common_prefix(v_peek_name, v_prefix, v_delimiter), v_delimiter) from v_prefix_len + 1);
                id := NULL;
                updated_at := NULL;
                created_at := NULL;
                last_accessed_at := NULL;
                metadata := NULL;
                version := NULL;
                archived_at := NULL;
                is_delete_marker := NULL;
                is_versioned := NULL;
                RETURN NEXT;
                v_count := v_count + 1;
            END IF;

            -- Advance seek past the folder range
            IF v_is_asc THEN
                v_next_seek := lower(left(v_common_prefix, -1)) || chr(ascii(v_delimiter) + 1);
            ELSE
                v_next_seek := lower(v_common_prefix);
            END IF;
            v_next_seek_at := NULL;
            v_next_seek_version := '';
        ELSE
            -- FILE: Batch fetch using DYNAMIC SQL (overhead amortized over many rows)
            -- For ASC: upper_bound is the exclusive upper limit (< condition)
            -- For DESC: prefix_lower is the inclusive lower limit (>= condition)
            FOR v_current IN EXECUTE v_batch_query
                USING bucketname, v_next_seek,
                    CASE WHEN v_is_asc THEN COALESCE(v_upper_bound, v_prefix_lower) ELSE v_prefix_lower END, v_file_batch_size,
                    v_next_seek_at, v_next_seek_version
            LOOP
                v_common_prefix := storage.get_common_prefix(lower(v_current.name), v_prefix_lower, v_delimiter);

                IF v_common_prefix IS NOT NULL THEN
                    -- Hit a folder: exit batch, let peek handle it. Reset
                    -- strict mode too - it may have been set by an earlier
                    -- row in this same batch (see the single-row ASC advance
                    -- below), and v_next_seek here is the folder-triggering
                    -- row's own name, which the next peek must find inclusively.
                    v_next_seek := CASE
                        WHEN v_is_asc THEN lower(v_current.name)
                        ELSE lower(v_current.name) || v_delimiter
                    END;
                    v_next_seek_at := NULL;
                    v_next_seek_version := '';
                    v_next_seek_strict := false;
                    EXIT;
                END IF;

                -- Handle offset skipping
                IF v_skipped < offsets THEN
                    v_skipped := v_skipped + 1;
                ELSE
                    -- Emit file
                    name := substring(v_current.name from v_prefix_len + 1);
                    id := v_current.id;
                    updated_at := v_current.updated_at;
                    created_at := v_current.created_at;
                    last_accessed_at := v_current.last_accessed_at;
                    metadata := v_current.metadata;
                    version := v_current.version;
                    archived_at := v_current.archived_at;
                    is_delete_marker := v_current.is_delete_marker;
                    is_versioned := v_current.is_versioned;
                    RETURN NEXT;
                    v_count := v_count + 1;
                END IF;

                -- Multi-row mode must remain on this key until all of its
                -- versions have crossed the internal batch boundary.
                IF v_multi_row THEN
                    v_next_seek := lower(v_current.name);
                    v_next_seek_at := COALESCE(v_current.archived_at, 'infinity'::timestamptz);
                    v_next_seek_version := COALESCE(v_current.version, '');
                ELSIF v_is_asc THEN
                    -- Appending the delimiter as a fake lexical successor would
                    -- skip a real key like `name || '!'` (or any character
                    -- sorting below the delimiter), which sorts between `name`
                    -- and `name || delimiter`. Track the real name and mark the
                    -- next comparison strict instead - same fix as the
                    -- exhausted-key case above.
                    v_next_seek := lower(v_current.name);
                    v_next_seek_strict := true;
                ELSE
                    v_next_seek := lower(v_current.name);
                END IF;

                EXIT WHEN v_count >= v_limit;
            END LOOP;
        END IF;

        IF v_count = v_previous_count
           AND v_skipped = v_previous_skipped
           AND v_next_seek IS NOT DISTINCT FROM v_previous_seek
           AND v_next_seek_at IS NOT DISTINCT FROM v_previous_seek_at
           AND v_next_seek_version IS NOT DISTINCT FROM v_previous_seek_version THEN
            RAISE EXCEPTION 'storage.search made no progress at seek (%, %, %)',
                v_next_seek, v_next_seek_at, v_next_seek_version;
        END IF;
    END LOOP;
END;
$_$;


ALTER FUNCTION storage.search(prefix text, bucketname text, limits integer, levels integer, offsets integer, search text, sortcolumn text, sortorder text, noncurrent_versions text, delete_markers text) OWNER TO supabase_storage_admin;

--
-- Name: search_by_timestamp(text, text, integer, integer, text, text, text, text, text, text, text); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.search_by_timestamp(p_prefix text, p_bucket_id text, p_limit integer, p_level integer, p_start_after text, p_sort_order text, p_sort_column text, p_sort_column_after text, noncurrent_versions text DEFAULT 'exclude'::text, delete_markers text DEFAULT 'exclude'::text, p_start_after_version text DEFAULT ''::text) RETURNS TABLE(key text, name text, id uuid, updated_at timestamp with time zone, created_at timestamp with time zone, last_accessed_at timestamp with time zone, metadata jsonb, version text, archived_at timestamp with time zone, is_delete_marker boolean, is_versioned boolean)
    LANGUAGE plpgsql STABLE
    AS $_$
DECLARE
    v_cursor_op text;
    v_query text;
    v_prefix text;
    v_prefix_pattern text;
    v_sort_order text;
    v_sort_column text;
    v_version_tiebreak text;
BEGIN
    v_prefix := coalesce(p_prefix, '');
    -- Keep the raw prefix for common-prefix calculations and escape only LIKE metacharacters.
    v_prefix_pattern := replace(v_prefix, chr(92), chr(92) || chr(92));
    v_prefix_pattern := replace(v_prefix_pattern, '%', chr(92) || '%');
    v_prefix_pattern := replace(v_prefix_pattern, '_', chr(92) || '_');

    -- COALESCE first: NULL NOT IN (...) evaluates to NULL (not TRUE), so a
    -- bare NOT IN check silently leaves an explicit NULL argument unreset.
    noncurrent_versions := COALESCE(noncurrent_versions, 'exclude');
    delete_markers := COALESCE(delete_markers, 'exclude');
    IF noncurrent_versions NOT IN ('exclude', 'only', 'include') THEN
        noncurrent_versions := 'exclude';
    END IF;
    IF delete_markers NOT IN ('exclude', 'only', 'include') THEN
        delete_markers := 'exclude';
    END IF;

    -- $9 is only populated in multi-row mode; it's always '' otherwise, so
    -- only use each row's real version as a tiebreak in multi-row mode.
    v_version_tiebreak := CASE WHEN noncurrent_versions IN ('only', 'include') THEN 'COALESCE(version, '''')' ELSE '''''' END;

    -- Defense-in-depth: this function is independently reachable and must
    -- not trust p_sort_order/p_sort_column to already be validated by a
    -- caller. Normalize to the same strict allow-list storage.search_v2
    -- uses before interpolating anything into dynamic SQL below.
    v_sort_order := lower(coalesce(p_sort_order, 'asc'));
    IF v_sort_order NOT IN ('asc', 'desc') THEN
        v_sort_order := 'asc';
    END IF;

    v_sort_column := lower(coalesce(p_sort_column, 'updated_at'));
    IF v_sort_column NOT IN ('updated_at', 'created_at') THEN
        v_sort_column := 'updated_at';
    END IF;

    IF v_sort_order = 'asc' THEN
        v_cursor_op := '>';
    ELSE
        v_cursor_op := '<';
    END IF;

    v_query := format($sql$
        WITH raw_objects AS (
            SELECT
                o.name AS obj_name,
                o.id AS obj_id,
                o.updated_at AS obj_updated_at,
                o.created_at AS obj_created_at,
                o.last_accessed_at AS obj_last_accessed_at,
                o.metadata AS obj_metadata,
                o.version AS obj_version,
                o.archived_at AS obj_archived_at,
                o.is_delete_marker AS obj_is_delete_marker,
                o.is_versioned AS obj_is_versioned,
                storage.get_common_prefix(o.name, $1, '/') AS common_prefix
            FROM storage.objects o
            WHERE o.bucket_id = $2
              AND o.name COLLATE "C" LIKE $10 || '%%'
              AND ($7 != 'exclude' OR o.archived_at IS NULL)
              AND ($7 != 'only' OR o.archived_at IS NOT NULL)
              AND ($8 != 'exclude' OR NOT o.is_delete_marker)
              AND ($8 != 'only' OR o.is_delete_marker)
        ),
        -- Aggregate common prefixes (folders)
        -- Both created_at and updated_at use MIN(obj_created_at) to match the old prefixes table behavior
        aggregated_prefixes AS (
            SELECT
                common_prefix AS name,
                NULL::uuid AS id,
                MIN(obj_created_at) AS updated_at,
                MIN(obj_created_at) AS created_at,
                NULL::timestamptz AS last_accessed_at,
                NULL::jsonb AS metadata,
                NULL::text AS version,
                NULL::timestamptz AS archived_at,
                NULL::boolean AS is_delete_marker,
                NULL::boolean AS is_versioned,
                TRUE AS is_prefix
            FROM raw_objects
            WHERE common_prefix IS NOT NULL
            GROUP BY common_prefix
        ),
        leaf_objects AS (
            SELECT
                obj_name AS name,
                obj_id AS id,
                obj_updated_at AS updated_at,
                obj_created_at AS created_at,
                obj_last_accessed_at AS last_accessed_at,
                obj_metadata AS metadata,
                obj_version AS version,
                obj_archived_at AS archived_at,
                obj_is_delete_marker AS is_delete_marker,
                obj_is_versioned AS is_versioned,
                FALSE AS is_prefix
            FROM raw_objects
            WHERE common_prefix IS NULL
        ),
        combined AS (
            SELECT * FROM aggregated_prefixes
            UNION ALL
            SELECT * FROM leaf_objects
        ),
        filtered AS (
            SELECT *
            FROM combined
            WHERE (
                $5 = ''
                OR ROW(
                    COALESCE(date_trunc('milliseconds', %I), 'epoch'::timestamptz),
                    name COLLATE "C",
                    %s
                ) %s ROW(
                    -- truncated the same way as the stored value above
                    date_trunc('milliseconds', COALESCE(NULLIF($6, '')::timestamptz, 'epoch'::timestamptz)),
                    $5,
                    $9
                )
            )
        )
        SELECT
            split_part(name, '/', $3) AS key,
            name,
            id,
            updated_at,
            created_at,
            last_accessed_at,
            metadata,
            version,
            archived_at,
            is_delete_marker,
            is_versioned
        FROM filtered
        ORDER BY
            COALESCE(date_trunc('milliseconds', %I), 'epoch'::timestamptz) %s,
            name COLLATE "C" %s,
            COALESCE(version, '') %s
        LIMIT $4
    $sql$,
        v_sort_column,
        v_version_tiebreak,
        v_cursor_op,
        v_sort_column,
        v_sort_order,
        v_sort_order,
        v_sort_order
    );

    -- version is the third tiebreak component for two versions of the same
    -- key tying on both timestamp and name (see filtered CTE / ORDER BY above)
    RETURN QUERY EXECUTE v_query
    USING v_prefix, p_bucket_id, p_level, p_limit, p_start_after, p_sort_column_after, noncurrent_versions, delete_markers, coalesce(p_start_after_version, ''), v_prefix_pattern;
END;
$_$;


ALTER FUNCTION storage.search_by_timestamp(p_prefix text, p_bucket_id text, p_limit integer, p_level integer, p_start_after text, p_sort_order text, p_sort_column text, p_sort_column_after text, noncurrent_versions text, delete_markers text, p_start_after_version text) OWNER TO supabase_storage_admin;

--
-- Name: search_v2(text, text, integer, integer, text, text, text, text, text, text, timestamp with time zone, text, boolean); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.search_v2(prefix text, bucket_name text, limits integer DEFAULT 100, levels integer DEFAULT 1, start_after text DEFAULT ''::text, sort_order text DEFAULT 'asc'::text, sort_column text DEFAULT 'name'::text, sort_column_after text DEFAULT ''::text, noncurrent_versions text DEFAULT 'exclude'::text, delete_markers text DEFAULT 'exclude'::text, start_after_archived_at timestamp with time zone DEFAULT NULL::timestamp with time zone, start_after_version text DEFAULT ''::text, start_after_is_continuation boolean DEFAULT false) RETURNS TABLE(key text, name text, id uuid, updated_at timestamp with time zone, created_at timestamp with time zone, last_accessed_at timestamp with time zone, metadata jsonb, version text, archived_at timestamp with time zone, is_delete_marker boolean, is_versioned boolean)
    LANGUAGE plpgsql STABLE
    AS $$
DECLARE
    v_sort_col text;
    v_sort_ord text;
    v_limit int;
BEGIN
    -- Cap limit to maximum of 1500 records
    v_limit := LEAST(coalesce(limits, 100), 1500);

    -- Validate and normalize sort_order
    v_sort_ord := lower(coalesce(sort_order, 'asc'));
    IF v_sort_ord NOT IN ('asc', 'desc') THEN
        v_sort_ord := 'asc';
    END IF;

    -- Validate and normalize sort_column
    v_sort_col := lower(coalesce(sort_column, 'name'));
    IF v_sort_col NOT IN ('name', 'updated_at', 'created_at') THEN
        v_sort_col := 'name';
    END IF;

    -- Route to appropriate implementation
    IF v_sort_col = 'name' THEN
        -- Use list_objects_with_delimiter for name sorting (most efficient: O(k * log n))
        RETURN QUERY
        SELECT
            split_part(l.name, '/', levels) AS key,
            l.name AS name,
            l.id,
            l.updated_at,
            l.created_at,
            l.last_accessed_at,
            l.metadata,
            l.version,
            l.archived_at,
            l.is_delete_marker,
            l.is_versioned
        FROM storage.list_objects_with_delimiter(
            bucket_name,
            coalesce(prefix, ''),
            '/',
            v_limit,
            CASE WHEN start_after_is_continuation THEN '' ELSE start_after END,
            CASE WHEN start_after_is_continuation THEN start_after ELSE '' END,
            v_sort_ord,
            noncurrent_versions,
            delete_markers,
            start_after_archived_at,
            start_after_version
        ) l;
    ELSE
        -- Use aggregation approach for timestamp sorting
        -- Not efficient for large datasets but supports correct pagination
        RETURN QUERY SELECT * FROM storage.search_by_timestamp(
            prefix, bucket_name, v_limit, levels, start_after,
            v_sort_ord, v_sort_col, sort_column_after,
            noncurrent_versions, delete_markers, start_after_version
        );
    END IF;
END;
$$;


ALTER FUNCTION storage.search_v2(prefix text, bucket_name text, limits integer, levels integer, start_after text, sort_order text, sort_column text, sort_column_after text, noncurrent_versions text, delete_markers text, start_after_archived_at timestamp with time zone, start_after_version text, start_after_is_continuation boolean) OWNER TO supabase_storage_admin;

--
-- Name: update_updated_at_column(); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.update_updated_at_column() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    NEW.updated_at = now();
    RETURN NEW; 
END;
$$;


ALTER FUNCTION storage.update_updated_at_column() OWNER TO supabase_storage_admin;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: audit_log_entries; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.audit_log_entries (
    instance_id uuid,
    id uuid NOT NULL,
    payload json,
    created_at timestamp with time zone,
    ip_address character varying(64) DEFAULT ''::character varying NOT NULL
);


ALTER TABLE auth.audit_log_entries OWNER TO supabase_auth_admin;

--
-- Name: TABLE audit_log_entries; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.audit_log_entries IS 'Auth: Audit trail for user actions.';


--
-- Name: custom_oauth_providers; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.custom_oauth_providers (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    provider_type text NOT NULL,
    identifier text NOT NULL,
    name text NOT NULL,
    client_id text NOT NULL,
    client_secret text NOT NULL,
    acceptable_client_ids text[] DEFAULT '{}'::text[] NOT NULL,
    scopes text[] DEFAULT '{}'::text[] NOT NULL,
    pkce_enabled boolean DEFAULT true NOT NULL,
    attribute_mapping jsonb DEFAULT '{}'::jsonb NOT NULL,
    authorization_params jsonb DEFAULT '{}'::jsonb NOT NULL,
    enabled boolean DEFAULT true NOT NULL,
    email_optional boolean DEFAULT false NOT NULL,
    issuer text,
    discovery_url text,
    skip_nonce_check boolean DEFAULT false NOT NULL,
    cached_discovery jsonb,
    discovery_cached_at timestamp with time zone,
    authorization_url text,
    token_url text,
    userinfo_url text,
    jwks_uri text,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL,
    custom_claims_allowlist text[] DEFAULT '{}'::text[] NOT NULL,
    CONSTRAINT custom_oauth_providers_authorization_url_https CHECK (((authorization_url IS NULL) OR (authorization_url ~~ 'https://%'::text))),
    CONSTRAINT custom_oauth_providers_authorization_url_length CHECK (((authorization_url IS NULL) OR (char_length(authorization_url) <= 2048))),
    CONSTRAINT custom_oauth_providers_client_id_length CHECK (((char_length(client_id) >= 1) AND (char_length(client_id) <= 512))),
    CONSTRAINT custom_oauth_providers_discovery_url_length CHECK (((discovery_url IS NULL) OR (char_length(discovery_url) <= 2048))),
    CONSTRAINT custom_oauth_providers_identifier_format CHECK ((identifier ~ '^[a-z0-9][a-z0-9:-]{0,48}[a-z0-9]$'::text)),
    CONSTRAINT custom_oauth_providers_issuer_length CHECK (((issuer IS NULL) OR ((char_length(issuer) >= 1) AND (char_length(issuer) <= 2048)))),
    CONSTRAINT custom_oauth_providers_jwks_uri_https CHECK (((jwks_uri IS NULL) OR (jwks_uri ~~ 'https://%'::text))),
    CONSTRAINT custom_oauth_providers_jwks_uri_length CHECK (((jwks_uri IS NULL) OR (char_length(jwks_uri) <= 2048))),
    CONSTRAINT custom_oauth_providers_name_length CHECK (((char_length(name) >= 1) AND (char_length(name) <= 100))),
    CONSTRAINT custom_oauth_providers_oauth2_requires_endpoints CHECK (((provider_type <> 'oauth2'::text) OR ((authorization_url IS NOT NULL) AND (token_url IS NOT NULL) AND (userinfo_url IS NOT NULL)))),
    CONSTRAINT custom_oauth_providers_oidc_discovery_url_https CHECK (((provider_type <> 'oidc'::text) OR (discovery_url IS NULL) OR (discovery_url ~~ 'https://%'::text))),
    CONSTRAINT custom_oauth_providers_oidc_issuer_https CHECK (((provider_type <> 'oidc'::text) OR (issuer IS NULL) OR (issuer ~~ 'https://%'::text))),
    CONSTRAINT custom_oauth_providers_oidc_requires_issuer CHECK (((provider_type <> 'oidc'::text) OR (issuer IS NOT NULL))),
    CONSTRAINT custom_oauth_providers_provider_type_check CHECK ((provider_type = ANY (ARRAY['oauth2'::text, 'oidc'::text]))),
    CONSTRAINT custom_oauth_providers_token_url_https CHECK (((token_url IS NULL) OR (token_url ~~ 'https://%'::text))),
    CONSTRAINT custom_oauth_providers_token_url_length CHECK (((token_url IS NULL) OR (char_length(token_url) <= 2048))),
    CONSTRAINT custom_oauth_providers_userinfo_url_https CHECK (((userinfo_url IS NULL) OR (userinfo_url ~~ 'https://%'::text))),
    CONSTRAINT custom_oauth_providers_userinfo_url_length CHECK (((userinfo_url IS NULL) OR (char_length(userinfo_url) <= 2048)))
);


ALTER TABLE auth.custom_oauth_providers OWNER TO supabase_auth_admin;

--
-- Name: flow_state; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.flow_state (
    id uuid NOT NULL,
    user_id uuid,
    auth_code text,
    code_challenge_method auth.code_challenge_method,
    code_challenge text,
    provider_type text NOT NULL,
    provider_access_token text,
    provider_refresh_token text,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    authentication_method text NOT NULL,
    auth_code_issued_at timestamp with time zone,
    invite_token text,
    referrer text,
    oauth_client_state_id uuid,
    linking_target_id uuid,
    email_optional boolean DEFAULT false NOT NULL
);


ALTER TABLE auth.flow_state OWNER TO supabase_auth_admin;

--
-- Name: TABLE flow_state; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.flow_state IS 'Stores metadata for all OAuth/SSO login flows';


--
-- Name: identities; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.identities (
    provider_id text NOT NULL,
    user_id uuid NOT NULL,
    identity_data jsonb NOT NULL,
    provider text NOT NULL,
    last_sign_in_at timestamp with time zone,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    email text GENERATED ALWAYS AS (lower((identity_data ->> 'email'::text))) STORED,
    id uuid DEFAULT gen_random_uuid() NOT NULL
);


ALTER TABLE auth.identities OWNER TO supabase_auth_admin;

--
-- Name: TABLE identities; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.identities IS 'Auth: Stores identities associated to a user.';


--
-- Name: COLUMN identities.email; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON COLUMN auth.identities.email IS 'Auth: Email is a generated column that references the optional email property in the identity_data';


--
-- Name: instances; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.instances (
    id uuid NOT NULL,
    uuid uuid,
    raw_base_config text,
    created_at timestamp with time zone,
    updated_at timestamp with time zone
);


ALTER TABLE auth.instances OWNER TO supabase_auth_admin;

--
-- Name: TABLE instances; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.instances IS 'Auth: Manages users across multiple sites.';


--
-- Name: mfa_amr_claims; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.mfa_amr_claims (
    session_id uuid NOT NULL,
    created_at timestamp with time zone NOT NULL,
    updated_at timestamp with time zone NOT NULL,
    authentication_method text NOT NULL,
    id uuid NOT NULL
);


ALTER TABLE auth.mfa_amr_claims OWNER TO supabase_auth_admin;

--
-- Name: TABLE mfa_amr_claims; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.mfa_amr_claims IS 'auth: stores authenticator method reference claims for multi factor authentication';


--
-- Name: mfa_challenges; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.mfa_challenges (
    id uuid NOT NULL,
    factor_id uuid NOT NULL,
    created_at timestamp with time zone NOT NULL,
    verified_at timestamp with time zone,
    ip_address inet NOT NULL,
    otp_code text,
    web_authn_session_data jsonb
);


ALTER TABLE auth.mfa_challenges OWNER TO supabase_auth_admin;

--
-- Name: TABLE mfa_challenges; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.mfa_challenges IS 'auth: stores metadata about challenge requests made';


--
-- Name: mfa_factors; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.mfa_factors (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    friendly_name text,
    factor_type auth.factor_type NOT NULL,
    status auth.factor_status NOT NULL,
    created_at timestamp with time zone NOT NULL,
    updated_at timestamp with time zone NOT NULL,
    secret text,
    phone text,
    last_challenged_at timestamp with time zone,
    web_authn_credential jsonb,
    web_authn_aaguid uuid,
    last_webauthn_challenge_data jsonb
);


ALTER TABLE auth.mfa_factors OWNER TO supabase_auth_admin;

--
-- Name: TABLE mfa_factors; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.mfa_factors IS 'auth: stores metadata about factors';


--
-- Name: COLUMN mfa_factors.last_webauthn_challenge_data; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON COLUMN auth.mfa_factors.last_webauthn_challenge_data IS 'Stores the latest WebAuthn challenge data including attestation/assertion for customer verification';


--
-- Name: mfa_recovery_code_sets; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.mfa_recovery_code_sets (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    mfa_factor_id uuid NOT NULL,
    failed_verification_count integer DEFAULT 0 NOT NULL,
    verification_locked_until timestamp with time zone,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL,
    CONSTRAINT mfa_recovery_code_sets_failed_verification_count_check CHECK ((failed_verification_count >= 0))
);


ALTER TABLE auth.mfa_recovery_code_sets OWNER TO supabase_auth_admin;

--
-- Name: mfa_recovery_codes; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.mfa_recovery_codes (
    id uuid NOT NULL,
    mfa_recovery_code_set_id uuid NOT NULL,
    code_hash text NOT NULL,
    consumed_at timestamp with time zone,
    created_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE auth.mfa_recovery_codes OWNER TO supabase_auth_admin;

--
-- Name: oauth_authorizations; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.oauth_authorizations (
    id uuid NOT NULL,
    authorization_id text NOT NULL,
    client_id uuid NOT NULL,
    user_id uuid,
    redirect_uri text NOT NULL,
    scope text NOT NULL,
    state text,
    resource text,
    code_challenge text,
    code_challenge_method auth.code_challenge_method,
    response_type auth.oauth_response_type DEFAULT 'code'::auth.oauth_response_type NOT NULL,
    status auth.oauth_authorization_status DEFAULT 'pending'::auth.oauth_authorization_status NOT NULL,
    authorization_code text,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    expires_at timestamp with time zone DEFAULT (now() + '00:03:00'::interval) NOT NULL,
    approved_at timestamp with time zone,
    nonce text,
    CONSTRAINT oauth_authorizations_authorization_code_length CHECK ((char_length(authorization_code) <= 255)),
    CONSTRAINT oauth_authorizations_code_challenge_length CHECK ((char_length(code_challenge) <= 128)),
    CONSTRAINT oauth_authorizations_expires_at_future CHECK ((expires_at > created_at)),
    CONSTRAINT oauth_authorizations_nonce_length CHECK ((char_length(nonce) <= 255)),
    CONSTRAINT oauth_authorizations_redirect_uri_length CHECK ((char_length(redirect_uri) <= 2048)),
    CONSTRAINT oauth_authorizations_resource_length CHECK ((char_length(resource) <= 2048)),
    CONSTRAINT oauth_authorizations_scope_length CHECK ((char_length(scope) <= 4096)),
    CONSTRAINT oauth_authorizations_state_length CHECK ((char_length(state) <= 4096))
);


ALTER TABLE auth.oauth_authorizations OWNER TO supabase_auth_admin;

--
-- Name: oauth_client_states; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.oauth_client_states (
    id uuid NOT NULL,
    provider_type text NOT NULL,
    code_verifier text,
    created_at timestamp with time zone NOT NULL
);


ALTER TABLE auth.oauth_client_states OWNER TO supabase_auth_admin;

--
-- Name: TABLE oauth_client_states; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.oauth_client_states IS 'Stores OAuth states for third-party provider authentication flows where Supabase acts as the OAuth client.';


--
-- Name: oauth_clients; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.oauth_clients (
    id uuid NOT NULL,
    client_secret_hash text,
    registration_type auth.oauth_registration_type NOT NULL,
    redirect_uris text NOT NULL,
    grant_types text NOT NULL,
    client_name text,
    client_uri text,
    logo_uri text,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL,
    deleted_at timestamp with time zone,
    client_type auth.oauth_client_type DEFAULT 'confidential'::auth.oauth_client_type NOT NULL,
    token_endpoint_auth_method text NOT NULL,
    CONSTRAINT oauth_clients_client_name_length CHECK ((char_length(client_name) <= 1024)),
    CONSTRAINT oauth_clients_client_uri_length CHECK ((char_length(client_uri) <= 2048)),
    CONSTRAINT oauth_clients_logo_uri_length CHECK ((char_length(logo_uri) <= 2048)),
    CONSTRAINT oauth_clients_token_endpoint_auth_method_check CHECK ((token_endpoint_auth_method = ANY (ARRAY['client_secret_basic'::text, 'client_secret_post'::text, 'none'::text])))
);


ALTER TABLE auth.oauth_clients OWNER TO supabase_auth_admin;

--
-- Name: oauth_consents; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.oauth_consents (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    client_id uuid NOT NULL,
    scopes text NOT NULL,
    granted_at timestamp with time zone DEFAULT now() NOT NULL,
    revoked_at timestamp with time zone,
    CONSTRAINT oauth_consents_revoked_after_granted CHECK (((revoked_at IS NULL) OR (revoked_at >= granted_at))),
    CONSTRAINT oauth_consents_scopes_length CHECK ((char_length(scopes) <= 2048)),
    CONSTRAINT oauth_consents_scopes_not_empty CHECK ((char_length(TRIM(BOTH FROM scopes)) > 0))
);


ALTER TABLE auth.oauth_consents OWNER TO supabase_auth_admin;

--
-- Name: one_time_tokens; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.one_time_tokens (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    token_type auth.one_time_token_type NOT NULL,
    token_hash text NOT NULL,
    relates_to text NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    expires_at timestamp with time zone,
    CONSTRAINT one_time_tokens_token_hash_check CHECK ((char_length(token_hash) > 0))
);


ALTER TABLE auth.one_time_tokens OWNER TO supabase_auth_admin;

--
-- Name: refresh_tokens; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.refresh_tokens (
    instance_id uuid,
    id bigint NOT NULL,
    token character varying(255),
    user_id character varying(255),
    revoked boolean,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    parent character varying(255),
    session_id uuid
);


ALTER TABLE auth.refresh_tokens OWNER TO supabase_auth_admin;

--
-- Name: TABLE refresh_tokens; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.refresh_tokens IS 'Auth: Store of tokens used to refresh JWT tokens once they expire.';


--
-- Name: refresh_tokens_id_seq; Type: SEQUENCE; Schema: auth; Owner: supabase_auth_admin
--

CREATE SEQUENCE auth.refresh_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE auth.refresh_tokens_id_seq OWNER TO supabase_auth_admin;

--
-- Name: refresh_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: auth; Owner: supabase_auth_admin
--

ALTER SEQUENCE auth.refresh_tokens_id_seq OWNED BY auth.refresh_tokens.id;


--
-- Name: saml_providers; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.saml_providers (
    id uuid NOT NULL,
    sso_provider_id uuid NOT NULL,
    entity_id text NOT NULL,
    metadata_xml text NOT NULL,
    metadata_url text,
    attribute_mapping jsonb,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    name_id_format text,
    CONSTRAINT "entity_id not empty" CHECK ((char_length(entity_id) > 0)),
    CONSTRAINT "metadata_url not empty" CHECK (((metadata_url = NULL::text) OR (char_length(metadata_url) > 0))),
    CONSTRAINT "metadata_xml not empty" CHECK ((char_length(metadata_xml) > 0))
);


ALTER TABLE auth.saml_providers OWNER TO supabase_auth_admin;

--
-- Name: TABLE saml_providers; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.saml_providers IS 'Auth: Manages SAML Identity Provider connections.';


--
-- Name: saml_relay_states; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.saml_relay_states (
    id uuid NOT NULL,
    sso_provider_id uuid NOT NULL,
    request_id text NOT NULL,
    for_email text,
    redirect_to text,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    flow_state_id uuid,
    CONSTRAINT "request_id not empty" CHECK ((char_length(request_id) > 0))
);


ALTER TABLE auth.saml_relay_states OWNER TO supabase_auth_admin;

--
-- Name: TABLE saml_relay_states; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.saml_relay_states IS 'Auth: Contains SAML Relay State information for each Service Provider initiated login.';


--
-- Name: schema_migrations; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.schema_migrations (
    version character varying(255) NOT NULL
);


ALTER TABLE auth.schema_migrations OWNER TO supabase_auth_admin;

--
-- Name: TABLE schema_migrations; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.schema_migrations IS 'Auth: Manages updates to the auth system.';


--
-- Name: scim_tokens; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.scim_tokens (
    id uuid NOT NULL,
    sso_provider_id uuid NOT NULL,
    token_hash text NOT NULL,
    prefix text NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    expires_at timestamp with time zone,
    revoked_at timestamp with time zone,
    last_used_at timestamp with time zone,
    CONSTRAINT scim_tokens_expires_at_future CHECK (((expires_at IS NULL) OR (expires_at > created_at))),
    CONSTRAINT scim_tokens_revoked_after_created CHECK (((revoked_at IS NULL) OR (revoked_at >= created_at))),
    CONSTRAINT scim_tokens_token_hash_check CHECK ((token_hash ~ '^[0-9a-f]{64}$'::text))
);


ALTER TABLE auth.scim_tokens OWNER TO supabase_auth_admin;

--
-- Name: scim_users; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.scim_users (
    id uuid NOT NULL,
    sso_provider_id uuid NOT NULL,
    user_id uuid,
    resource jsonb NOT NULL,
    user_name text GENERATED ALWAYS AS (lower((resource ->> 'userName'::text))) STORED NOT NULL,
    external_id text GENERATED ALWAYS AS ((resource ->> 'externalId'::text)) STORED,
    active boolean GENERATED ALWAYS AS (COALESCE(((resource ->> 'active'::text))::boolean, true)) STORED NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL,
    deleted_at timestamp with time zone
);


ALTER TABLE auth.scim_users OWNER TO supabase_auth_admin;

--
-- Name: sessions; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.sessions (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    factor_id uuid,
    aal auth.aal_level,
    not_after timestamp with time zone,
    refreshed_at timestamp without time zone,
    user_agent text,
    ip inet,
    tag text,
    oauth_client_id uuid,
    refresh_token_hmac_key text,
    refresh_token_counter bigint,
    scopes text,
    CONSTRAINT sessions_scopes_length CHECK ((char_length(scopes) <= 4096))
);


ALTER TABLE auth.sessions OWNER TO supabase_auth_admin;

--
-- Name: TABLE sessions; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.sessions IS 'Auth: Stores session data associated to a user.';


--
-- Name: COLUMN sessions.not_after; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON COLUMN auth.sessions.not_after IS 'Auth: Not after is a nullable column that contains a timestamp after which the session should be regarded as expired.';


--
-- Name: COLUMN sessions.refresh_token_hmac_key; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON COLUMN auth.sessions.refresh_token_hmac_key IS 'Holds a HMAC-SHA256 key used to sign refresh tokens for this session.';


--
-- Name: COLUMN sessions.refresh_token_counter; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON COLUMN auth.sessions.refresh_token_counter IS 'Holds the ID (counter) of the last issued refresh token.';


--
-- Name: sso_domains; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.sso_domains (
    id uuid NOT NULL,
    sso_provider_id uuid NOT NULL,
    domain text NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    CONSTRAINT "domain not empty" CHECK ((char_length(domain) > 0))
);


ALTER TABLE auth.sso_domains OWNER TO supabase_auth_admin;

--
-- Name: TABLE sso_domains; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.sso_domains IS 'Auth: Manages SSO email address domain mapping to an SSO Identity Provider.';


--
-- Name: sso_providers; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.sso_providers (
    id uuid NOT NULL,
    resource_id text,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    disabled boolean,
    CONSTRAINT "resource_id not empty" CHECK (((resource_id = NULL::text) OR (char_length(resource_id) > 0)))
);


ALTER TABLE auth.sso_providers OWNER TO supabase_auth_admin;

--
-- Name: TABLE sso_providers; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.sso_providers IS 'Auth: Manages SSO identity provider information; see saml_providers for SAML.';


--
-- Name: COLUMN sso_providers.resource_id; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON COLUMN auth.sso_providers.resource_id IS 'Auth: Uniquely identifies a SSO provider according to a user-chosen resource ID (case insensitive), useful in infrastructure as code.';


--
-- Name: users; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.users (
    instance_id uuid,
    id uuid NOT NULL,
    aud character varying(255),
    role character varying(255),
    email character varying(255),
    encrypted_password character varying(255),
    email_confirmed_at timestamp with time zone,
    invited_at timestamp with time zone,
    confirmation_token character varying(255),
    confirmation_sent_at timestamp with time zone,
    recovery_token character varying(255),
    recovery_sent_at timestamp with time zone,
    email_change_token_new character varying(255),
    email_change character varying(255),
    email_change_sent_at timestamp with time zone,
    last_sign_in_at timestamp with time zone,
    raw_app_meta_data jsonb,
    raw_user_meta_data jsonb,
    is_super_admin boolean,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    phone text DEFAULT NULL::character varying,
    phone_confirmed_at timestamp with time zone,
    phone_change text DEFAULT ''::character varying,
    phone_change_token character varying(255) DEFAULT ''::character varying,
    phone_change_sent_at timestamp with time zone,
    confirmed_at timestamp with time zone GENERATED ALWAYS AS (LEAST(email_confirmed_at, phone_confirmed_at)) STORED,
    email_change_token_current character varying(255) DEFAULT ''::character varying,
    email_change_confirm_status smallint DEFAULT 0,
    banned_until timestamp with time zone,
    reauthentication_token character varying(255) DEFAULT ''::character varying,
    reauthentication_sent_at timestamp with time zone,
    is_sso_user boolean DEFAULT false NOT NULL,
    deleted_at timestamp with time zone,
    is_anonymous boolean DEFAULT false NOT NULL,
    CONSTRAINT users_email_change_confirm_status_check CHECK (((email_change_confirm_status >= 0) AND (email_change_confirm_status <= 2)))
);


ALTER TABLE auth.users OWNER TO supabase_auth_admin;

--
-- Name: TABLE users; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.users IS 'Auth: Stores user login data within a secure schema.';


--
-- Name: COLUMN users.is_sso_user; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON COLUMN auth.users.is_sso_user IS 'Auth: Set this column to true when the account comes from SSO. These accounts can have duplicate emails.';


--
-- Name: webauthn_challenges; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.webauthn_challenges (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    user_id uuid,
    challenge_type text NOT NULL,
    session_data jsonb NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    expires_at timestamp with time zone NOT NULL,
    CONSTRAINT webauthn_challenges_challenge_type_check CHECK ((challenge_type = ANY (ARRAY['signup'::text, 'registration'::text, 'authentication'::text])))
);


ALTER TABLE auth.webauthn_challenges OWNER TO supabase_auth_admin;

--
-- Name: webauthn_credentials; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.webauthn_credentials (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    user_id uuid NOT NULL,
    credential_id bytea NOT NULL,
    public_key bytea NOT NULL,
    attestation_type text DEFAULT ''::text NOT NULL,
    aaguid uuid,
    sign_count bigint DEFAULT 0 NOT NULL,
    transports jsonb DEFAULT '[]'::jsonb NOT NULL,
    backup_eligible boolean DEFAULT false NOT NULL,
    backed_up boolean DEFAULT false NOT NULL,
    friendly_name text DEFAULT ''::text NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL,
    last_used_at timestamp with time zone
);


ALTER TABLE auth.webauthn_credentials OWNER TO supabase_auth_admin;

--
-- Name: attendance_report_months; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.attendance_report_months (
    id bigint NOT NULL,
    school_year_id bigint NOT NULL,
    month smallint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.attendance_report_months OWNER TO postgres;

--
-- Name: attendance_report_months_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.attendance_report_months_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.attendance_report_months_id_seq OWNER TO postgres;

--
-- Name: attendance_report_months_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.attendance_report_months_id_seq OWNED BY public.attendance_report_months.id;


--
-- Name: attendance_report_sections; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.attendance_report_sections (
    id bigint NOT NULL,
    attendance_report_month_id bigint NOT NULL,
    grade_level smallint NOT NULL,
    section character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.attendance_report_sections OWNER TO postgres;

--
-- Name: attendance_report_sections_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.attendance_report_sections_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.attendance_report_sections_id_seq OWNER TO postgres;

--
-- Name: attendance_report_sections_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.attendance_report_sections_id_seq OWNED BY public.attendance_report_sections.id;


--
-- Name: audit_logs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.audit_logs (
    id bigint NOT NULL,
    user_id bigint,
    action character varying(255) NOT NULL,
    module character varying(255) NOT NULL,
    description text NOT NULL,
    ip_address character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.audit_logs OWNER TO postgres;

--
-- Name: audit_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.audit_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.audit_logs_id_seq OWNER TO postgres;

--
-- Name: audit_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.audit_logs_id_seq OWNED BY public.audit_logs.id;


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
-- Name: enrollments; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.enrollments (
    id bigint NOT NULL,
    student_id bigint NOT NULL,
    school_year_id bigint NOT NULL,
    grade_level integer NOT NULL,
    section character varying(255) NOT NULL,
    status character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.enrollments OWNER TO postgres;

--
-- Name: enrollments_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.enrollments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.enrollments_id_seq OWNER TO postgres;

--
-- Name: enrollments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.enrollments_id_seq OWNED BY public.enrollments.id;


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
-- Name: jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
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
-- Name: meal_plans; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.meal_plans (
    id bigint NOT NULL,
    meal_date date NOT NULL,
    meal_name character varying(255) NOT NULL,
    recorded_by_user_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.meal_plans OWNER TO postgres;

--
-- Name: meal_plans_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.meal_plans_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.meal_plans_id_seq OWNER TO postgres;

--
-- Name: meal_plans_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.meal_plans_id_seq OWNED BY public.meal_plans.id;


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
-- Name: nutrition_measurements; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.nutrition_measurements (
    id bigint NOT NULL,
    sbfp_participant_id bigint NOT NULL,
    height character varying(255) NOT NULL,
    weight character varying(255) NOT NULL,
    bmi numeric(8,2) NOT NULL,
    bmi_category character varying(255) NOT NULL,
    hfa character varying(255) NOT NULL,
    measurement_period character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.nutrition_measurements OWNER TO postgres;

--
-- Name: nutrition_measurements_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.nutrition_measurements_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.nutrition_measurements_id_seq OWNER TO postgres;

--
-- Name: nutrition_measurements_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.nutrition_measurements_id_seq OWNED BY public.nutrition_measurements.id;


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
-- Name: report_period_rows; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.report_period_rows (
    id bigint NOT NULL,
    report_period_id bigint NOT NULL,
    grade_level smallint NOT NULL,
    sex character varying(1) NOT NULL,
    enrollment integer DEFAULT 0 NOT NULL,
    pupils_weighed integer DEFAULT 0 NOT NULL,
    bmi_severely_wasted integer DEFAULT 0 NOT NULL,
    bmi_wasted integer DEFAULT 0 NOT NULL,
    bmi_normal integer DEFAULT 0 NOT NULL,
    bmi_overweight integer DEFAULT 0 NOT NULL,
    bmi_obese integer DEFAULT 0 NOT NULL,
    hfa_severely_stunted integer DEFAULT 0 NOT NULL,
    hfa_stunted integer DEFAULT 0 NOT NULL,
    hfa_normal integer DEFAULT 0 NOT NULL,
    hfa_tall integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    pupils_height_taken integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.report_period_rows OWNER TO postgres;

--
-- Name: report_period_rows_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.report_period_rows_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.report_period_rows_id_seq OWNER TO postgres;

--
-- Name: report_period_rows_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.report_period_rows_id_seq OWNED BY public.report_period_rows.id;


--
-- Name: report_periods; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.report_periods (
    id bigint NOT NULL,
    school_year_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    month smallint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    measurement_period character varying(20) DEFAULT 'baseline'::character varying NOT NULL
);


ALTER TABLE public.report_periods OWNER TO postgres;

--
-- Name: report_periods_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.report_periods_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.report_periods_id_seq OWNER TO postgres;

--
-- Name: report_periods_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.report_periods_id_seq OWNED BY public.report_periods.id;


--
-- Name: sbfp_parent_approval_requests; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sbfp_parent_approval_requests (
    id bigint NOT NULL,
    sbfp_participant_id bigint NOT NULL,
    email character varying(255) NOT NULL,
    token_hash character varying(255) NOT NULL,
    weight numeric(8,2) NOT NULL,
    height numeric(8,2) NOT NULL,
    bmi numeric(8,2) NOT NULL,
    bmi_category character varying(255) NOT NULL,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    expires_at timestamp(0) without time zone NOT NULL,
    sent_at timestamp(0) without time zone,
    responded_at timestamp(0) without time zone,
    decision_reason character varying(255),
    closed_reason character varying(255),
    closed_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.sbfp_parent_approval_requests OWNER TO postgres;

--
-- Name: sbfp_parent_approval_requests_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sbfp_parent_approval_requests_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sbfp_parent_approval_requests_id_seq OWNER TO postgres;

--
-- Name: sbfp_parent_approval_requests_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sbfp_parent_approval_requests_id_seq OWNED BY public.sbfp_parent_approval_requests.id;


--
-- Name: sbfp_participants; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sbfp_participants (
    id bigint NOT NULL,
    enrollment_id bigint NOT NULL,
    parent_consent character varying(255),
    disapproval_reason text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    profile_image_url text
);


ALTER TABLE public.sbfp_participants OWNER TO postgres;

--
-- Name: sbfp_participants_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sbfp_participants_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sbfp_participants_id_seq OWNER TO postgres;

--
-- Name: sbfp_participants_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sbfp_participants_id_seq OWNED BY public.sbfp_participants.id;


--
-- Name: school_year_user_records; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.school_year_user_records (
    id bigint NOT NULL,
    school_year_id bigint NOT NULL,
    user_id bigint NOT NULL,
    role character varying(255) NOT NULL,
    deped_id bigint,
    "position" character varying(255),
    advisory_grade_level character varying(255),
    advisory_section character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.school_year_user_records OWNER TO postgres;

--
-- Name: school_year_user_records_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.school_year_user_records_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.school_year_user_records_id_seq OWNER TO postgres;

--
-- Name: school_year_user_records_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.school_year_user_records_id_seq OWNED BY public.school_year_user_records.id;


--
-- Name: school_years; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.school_years (
    id bigint NOT NULL,
    year character varying(255) NOT NULL,
    is_active boolean DEFAULT false NOT NULL,
    start_date date NOT NULL,
    end_date date,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.school_years OWNER TO postgres;

--
-- Name: school_years_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.school_years_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.school_years_id_seq OWNER TO postgres;

--
-- Name: school_years_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.school_years_id_seq OWNED BY public.school_years.id;


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
-- Name: student_attendance_records; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.student_attendance_records (
    id bigint NOT NULL,
    sbfp_participant_id bigint NOT NULL,
    recorded_by_user_id bigint NOT NULL,
    attendance_date date NOT NULL,
    status character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.student_attendance_records OWNER TO postgres;

--
-- Name: student_attendance_records_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.student_attendance_records_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.student_attendance_records_id_seq OWNER TO postgres;

--
-- Name: student_attendance_records_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.student_attendance_records_id_seq OWNED BY public.student_attendance_records.id;


--
-- Name: student_feeding_records; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.student_feeding_records (
    id bigint NOT NULL,
    sbfp_participant_id bigint NOT NULL,
    recorded_by_user_id bigint NOT NULL,
    feeding_date date NOT NULL,
    meal_type character varying(255) NOT NULL,
    meal_served character varying(255) NOT NULL,
    photo character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.student_feeding_records OWNER TO postgres;

--
-- Name: student_feeding_records_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.student_feeding_records_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.student_feeding_records_id_seq OWNER TO postgres;

--
-- Name: student_feeding_records_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.student_feeding_records_id_seq OWNED BY public.student_feeding_records.id;


--
-- Name: students; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.students (
    id bigint NOT NULL,
    lrn bigint NOT NULL,
    first_name character varying(255) NOT NULL,
    last_name character varying(255) NOT NULL,
    name_extension character varying(255),
    middle_name character varying(255),
    sex character varying(255) NOT NULL,
    birth_date date NOT NULL,
    guardian_name character varying(255) NOT NULL,
    guardian_email character varying(255),
    address character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    guardian_contact character varying(255)
);


ALTER TABLE public.students OWNER TO postgres;

--
-- Name: students_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.students_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.students_id_seq OWNER TO postgres;

--
-- Name: students_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.students_id_seq OWNED BY public.students.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    sex character varying(255) NOT NULL,
    role character varying(255) NOT NULL,
    birthdate date NOT NULL,
    "position" character varying(255) NOT NULL,
    advisory_grade_level character varying(255),
    advisory_section character varying(255),
    deped_id bigint,
    is_active boolean DEFAULT true NOT NULL,
    deleted_at timestamp(0) without time zone,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    first_name character varying(255),
    middle_name character varying(255),
    last_name character varying(255),
    name_extension character varying(255)
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
-- Name: messages; Type: TABLE; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE TABLE realtime.messages (
    topic text NOT NULL,
    extension text NOT NULL,
    payload jsonb,
    event text,
    private boolean DEFAULT false,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    inserted_at timestamp without time zone DEFAULT now() NOT NULL,
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    binary_payload bytea,
    skip_broadcast boolean DEFAULT false NOT NULL
)
PARTITION BY RANGE (inserted_at);


ALTER TABLE realtime.messages OWNER TO supabase_realtime_admin;

--
-- Name: schema_migrations; Type: TABLE; Schema: realtime; Owner: supabase_admin
--

CREATE TABLE realtime.schema_migrations (
    version bigint NOT NULL,
    inserted_at timestamp(0) without time zone
);


ALTER TABLE realtime.schema_migrations OWNER TO supabase_admin;

--
-- Name: subscription; Type: TABLE; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE TABLE realtime.subscription (
    id bigint NOT NULL,
    subscription_id uuid NOT NULL,
    entity regclass NOT NULL,
    filters realtime.user_defined_filter[] DEFAULT '{}'::realtime.user_defined_filter[] NOT NULL,
    claims jsonb NOT NULL,
    claims_role regrole GENERATED ALWAYS AS (realtime.to_regrole((claims ->> 'role'::text))) STORED NOT NULL,
    created_at timestamp without time zone DEFAULT timezone('utc'::text, now()) NOT NULL,
    action_filter text DEFAULT '*'::text,
    selected_columns text[],
    CONSTRAINT subscription_action_filter_check CHECK ((action_filter = ANY (ARRAY['*'::text, 'INSERT'::text, 'UPDATE'::text, 'DELETE'::text])))
);


ALTER TABLE realtime.subscription OWNER TO supabase_realtime_admin;

--
-- Name: subscription_id_seq; Type: SEQUENCE; Schema: realtime; Owner: supabase_realtime_admin
--

ALTER TABLE realtime.subscription ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME realtime.subscription_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: buckets; Type: TABLE; Schema: storage; Owner: supabase_storage_admin
--

CREATE TABLE storage.buckets (
    id text NOT NULL,
    name text NOT NULL,
    owner uuid,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now(),
    public boolean DEFAULT false,
    avif_autodetection boolean DEFAULT false,
    file_size_limit bigint,
    allowed_mime_types text[],
    owner_id text,
    type storage.buckettype DEFAULT 'STANDARD'::storage.buckettype NOT NULL,
    versioning_status text DEFAULT 'DISABLED'::text NOT NULL,
    lifecycle_configuration jsonb,
    lifecycle_configuration_generation uuid,
    CONSTRAINT buckets_lifecycle_configuration_pair_check CHECK (((lifecycle_configuration IS NULL) = (lifecycle_configuration_generation IS NULL))),
    CONSTRAINT buckets_lifecycle_configuration_shape_check CHECK (((lifecycle_configuration IS NULL) OR ((jsonb_typeof(lifecycle_configuration) = 'object'::text) AND (lifecycle_configuration ? 'rules'::text) AND
CASE
    WHEN (jsonb_typeof((lifecycle_configuration -> 'rules'::text)) = 'array'::text) THEN ((jsonb_array_length((lifecycle_configuration -> 'rules'::text)) >= 1) AND (jsonb_array_length((lifecycle_configuration -> 'rules'::text)) <= 1000))
    ELSE false
END))),
    CONSTRAINT buckets_lifecycle_configuration_standard_only_check CHECK (((type = 'STANDARD'::storage.buckettype) OR ((lifecycle_configuration IS NULL) AND (lifecycle_configuration_generation IS NULL)))),
    CONSTRAINT buckets_versioning_dark_check CHECK ((versioning_status = 'DISABLED'::text)),
    CONSTRAINT buckets_versioning_standard_only_check CHECK (((type = 'STANDARD'::storage.buckettype) OR (versioning_status = 'DISABLED'::text))),
    CONSTRAINT buckets_versioning_status_check CHECK ((versioning_status = ANY (ARRAY['DISABLED'::text, 'ENABLED'::text, 'SUSPENDED'::text])))
);


ALTER TABLE storage.buckets OWNER TO supabase_storage_admin;

--
-- Name: COLUMN buckets.owner; Type: COMMENT; Schema: storage; Owner: supabase_storage_admin
--

COMMENT ON COLUMN storage.buckets.owner IS 'Field is deprecated, use owner_id instead';


--
-- Name: buckets_analytics; Type: TABLE; Schema: storage; Owner: supabase_storage_admin
--

CREATE TABLE storage.buckets_analytics (
    name text NOT NULL,
    type storage.buckettype DEFAULT 'ANALYTICS'::storage.buckettype NOT NULL,
    format text DEFAULT 'ICEBERG'::text NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL,
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    deleted_at timestamp with time zone
);


ALTER TABLE storage.buckets_analytics OWNER TO supabase_storage_admin;

--
-- Name: buckets_vectors; Type: TABLE; Schema: storage; Owner: supabase_storage_admin
--

CREATE TABLE storage.buckets_vectors (
    id text NOT NULL,
    type storage.buckettype DEFAULT 'VECTOR'::storage.buckettype NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE storage.buckets_vectors OWNER TO supabase_storage_admin;

--
-- Name: migrations; Type: TABLE; Schema: storage; Owner: supabase_storage_admin
--

CREATE TABLE storage.migrations (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    hash character varying(40) NOT NULL,
    executed_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE storage.migrations OWNER TO supabase_storage_admin;

--
-- Name: objects; Type: TABLE; Schema: storage; Owner: supabase_storage_admin
--

CREATE TABLE storage.objects (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    bucket_id text,
    name text,
    owner uuid,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now(),
    last_accessed_at timestamp with time zone DEFAULT now(),
    metadata jsonb,
    path_tokens text[] GENERATED ALWAYS AS (string_to_array(name, '/'::text)) STORED,
    version text,
    owner_id text,
    user_metadata jsonb,
    archived_at timestamp with time zone,
    is_delete_marker boolean DEFAULT false NOT NULL,
    is_versioned boolean DEFAULT false NOT NULL
);


ALTER TABLE storage.objects OWNER TO supabase_storage_admin;

--
-- Name: COLUMN objects.owner; Type: COMMENT; Schema: storage; Owner: supabase_storage_admin
--

COMMENT ON COLUMN storage.objects.owner IS 'Field is deprecated, use owner_id instead';


--
-- Name: s3_multipart_uploads; Type: TABLE; Schema: storage; Owner: supabase_storage_admin
--

CREATE TABLE storage.s3_multipart_uploads (
    id text NOT NULL,
    in_progress_size bigint DEFAULT 0 NOT NULL,
    upload_signature text NOT NULL,
    bucket_id text NOT NULL,
    key text NOT NULL COLLATE pg_catalog."C",
    version text NOT NULL,
    owner_id text,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    user_metadata jsonb,
    metadata jsonb
);


ALTER TABLE storage.s3_multipart_uploads OWNER TO supabase_storage_admin;

--
-- Name: s3_multipart_uploads_parts; Type: TABLE; Schema: storage; Owner: supabase_storage_admin
--

CREATE TABLE storage.s3_multipart_uploads_parts (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    upload_id text NOT NULL,
    size bigint DEFAULT 0 NOT NULL,
    part_number integer NOT NULL,
    bucket_id text NOT NULL,
    key text NOT NULL COLLATE pg_catalog."C",
    etag text NOT NULL,
    owner_id text,
    version text NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE storage.s3_multipart_uploads_parts OWNER TO supabase_storage_admin;

--
-- Name: vector_indexes; Type: TABLE; Schema: storage; Owner: supabase_storage_admin
--

CREATE TABLE storage.vector_indexes (
    id text DEFAULT gen_random_uuid() NOT NULL,
    name text NOT NULL COLLATE pg_catalog."C",
    bucket_id text NOT NULL,
    data_type text NOT NULL,
    dimension integer NOT NULL,
    distance_metric text NOT NULL,
    metadata_configuration jsonb,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE storage.vector_indexes OWNER TO supabase_storage_admin;

--
-- Name: refresh_tokens id; Type: DEFAULT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.refresh_tokens ALTER COLUMN id SET DEFAULT nextval('auth.refresh_tokens_id_seq'::regclass);


--
-- Name: attendance_report_months id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attendance_report_months ALTER COLUMN id SET DEFAULT nextval('public.attendance_report_months_id_seq'::regclass);


--
-- Name: attendance_report_sections id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attendance_report_sections ALTER COLUMN id SET DEFAULT nextval('public.attendance_report_sections_id_seq'::regclass);


--
-- Name: audit_logs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.audit_logs ALTER COLUMN id SET DEFAULT nextval('public.audit_logs_id_seq'::regclass);


--
-- Name: enrollments id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.enrollments ALTER COLUMN id SET DEFAULT nextval('public.enrollments_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: meal_plans id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.meal_plans ALTER COLUMN id SET DEFAULT nextval('public.meal_plans_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: nutrition_measurements id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.nutrition_measurements ALTER COLUMN id SET DEFAULT nextval('public.nutrition_measurements_id_seq'::regclass);


--
-- Name: report_period_rows id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.report_period_rows ALTER COLUMN id SET DEFAULT nextval('public.report_period_rows_id_seq'::regclass);


--
-- Name: report_periods id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.report_periods ALTER COLUMN id SET DEFAULT nextval('public.report_periods_id_seq'::regclass);


--
-- Name: sbfp_parent_approval_requests id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sbfp_parent_approval_requests ALTER COLUMN id SET DEFAULT nextval('public.sbfp_parent_approval_requests_id_seq'::regclass);


--
-- Name: sbfp_participants id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sbfp_participants ALTER COLUMN id SET DEFAULT nextval('public.sbfp_participants_id_seq'::regclass);


--
-- Name: school_year_user_records id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.school_year_user_records ALTER COLUMN id SET DEFAULT nextval('public.school_year_user_records_id_seq'::regclass);


--
-- Name: school_years id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.school_years ALTER COLUMN id SET DEFAULT nextval('public.school_years_id_seq'::regclass);


--
-- Name: student_attendance_records id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.student_attendance_records ALTER COLUMN id SET DEFAULT nextval('public.student_attendance_records_id_seq'::regclass);


--
-- Name: student_feeding_records id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.student_feeding_records ALTER COLUMN id SET DEFAULT nextval('public.student_feeding_records_id_seq'::regclass);


--
-- Name: students id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.students ALTER COLUMN id SET DEFAULT nextval('public.students_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Data for Name: audit_log_entries; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.audit_log_entries (instance_id, id, payload, created_at, ip_address) FROM stdin;
\.


--
-- Data for Name: custom_oauth_providers; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.custom_oauth_providers (id, provider_type, identifier, name, client_id, client_secret, acceptable_client_ids, scopes, pkce_enabled, attribute_mapping, authorization_params, enabled, email_optional, issuer, discovery_url, skip_nonce_check, cached_discovery, discovery_cached_at, authorization_url, token_url, userinfo_url, jwks_uri, created_at, updated_at, custom_claims_allowlist) FROM stdin;
\.


--
-- Data for Name: flow_state; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.flow_state (id, user_id, auth_code, code_challenge_method, code_challenge, provider_type, provider_access_token, provider_refresh_token, created_at, updated_at, authentication_method, auth_code_issued_at, invite_token, referrer, oauth_client_state_id, linking_target_id, email_optional) FROM stdin;
\.


--
-- Data for Name: identities; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.identities (provider_id, user_id, identity_data, provider, last_sign_in_at, created_at, updated_at, id) FROM stdin;
\.


--
-- Data for Name: instances; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.instances (id, uuid, raw_base_config, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: mfa_amr_claims; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.mfa_amr_claims (session_id, created_at, updated_at, authentication_method, id) FROM stdin;
\.


--
-- Data for Name: mfa_challenges; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.mfa_challenges (id, factor_id, created_at, verified_at, ip_address, otp_code, web_authn_session_data) FROM stdin;
\.


--
-- Data for Name: mfa_factors; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.mfa_factors (id, user_id, friendly_name, factor_type, status, created_at, updated_at, secret, phone, last_challenged_at, web_authn_credential, web_authn_aaguid, last_webauthn_challenge_data) FROM stdin;
\.


--
-- Data for Name: mfa_recovery_code_sets; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.mfa_recovery_code_sets (id, user_id, mfa_factor_id, failed_verification_count, verification_locked_until, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: mfa_recovery_codes; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.mfa_recovery_codes (id, mfa_recovery_code_set_id, code_hash, consumed_at, created_at) FROM stdin;
\.


--
-- Data for Name: oauth_authorizations; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.oauth_authorizations (id, authorization_id, client_id, user_id, redirect_uri, scope, state, resource, code_challenge, code_challenge_method, response_type, status, authorization_code, created_at, expires_at, approved_at, nonce) FROM stdin;
\.


--
-- Data for Name: oauth_client_states; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.oauth_client_states (id, provider_type, code_verifier, created_at) FROM stdin;
\.


--
-- Data for Name: oauth_clients; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.oauth_clients (id, client_secret_hash, registration_type, redirect_uris, grant_types, client_name, client_uri, logo_uri, created_at, updated_at, deleted_at, client_type, token_endpoint_auth_method) FROM stdin;
\.


--
-- Data for Name: oauth_consents; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.oauth_consents (id, user_id, client_id, scopes, granted_at, revoked_at) FROM stdin;
\.


--
-- Data for Name: one_time_tokens; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.one_time_tokens (id, user_id, token_type, token_hash, relates_to, created_at, updated_at, expires_at) FROM stdin;
\.


--
-- Data for Name: refresh_tokens; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.refresh_tokens (instance_id, id, token, user_id, revoked, created_at, updated_at, parent, session_id) FROM stdin;
\.


--
-- Data for Name: saml_providers; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.saml_providers (id, sso_provider_id, entity_id, metadata_xml, metadata_url, attribute_mapping, created_at, updated_at, name_id_format) FROM stdin;
\.


--
-- Data for Name: saml_relay_states; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.saml_relay_states (id, sso_provider_id, request_id, for_email, redirect_to, created_at, updated_at, flow_state_id) FROM stdin;
\.


--
-- Data for Name: schema_migrations; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.schema_migrations (version) FROM stdin;
20171026211738
20171026211808
20171026211834
20180103212743
20180108183307
20180119214651
20180125194653
00
20210710035447
20210722035447
20210730183235
20210909172000
20210927181326
20211122151130
20211124214934
20211202183645
20220114185221
20220114185340
20220224000811
20220323170000
20220429102000
20220531120530
20220614074223
20220811173540
20221003041349
20221003041400
20221011041400
20221020193600
20221021073300
20221021082433
20221027105023
20221114143122
20221114143410
20221125140132
20221208132122
20221215195500
20221215195800
20221215195900
20230116124310
20230116124412
20230131181311
20230322519590
20230402418590
20230411005111
20230508135423
20230523124323
20230818113222
20230914180801
20231027141322
20231114161723
20231117164230
20240115144230
20240214120130
20240306115329
20240314092811
20240427152123
20240612123726
20240729123726
20240802193726
20240806073726
20241009103726
20250717082212
20250731150234
20250804100000
20250901200500
20250903112500
20250904133000
20250925093508
20251007112900
20251104100000
20251111201300
20251201000000
20260115000000
20260121000000
20260219120000
20260302000000
20260625000000
20260821000000
20260821010000
20260824000000
20260824000001
20260831180000
\.


--
-- Data for Name: scim_tokens; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.scim_tokens (id, sso_provider_id, token_hash, prefix, created_at, expires_at, revoked_at, last_used_at) FROM stdin;
\.


--
-- Data for Name: scim_users; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.scim_users (id, sso_provider_id, user_id, resource, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.sessions (id, user_id, created_at, updated_at, factor_id, aal, not_after, refreshed_at, user_agent, ip, tag, oauth_client_id, refresh_token_hmac_key, refresh_token_counter, scopes) FROM stdin;
\.


--
-- Data for Name: sso_domains; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.sso_domains (id, sso_provider_id, domain, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: sso_providers; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.sso_providers (id, resource_id, created_at, updated_at, disabled) FROM stdin;
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.users (instance_id, id, aud, role, email, encrypted_password, email_confirmed_at, invited_at, confirmation_token, confirmation_sent_at, recovery_token, recovery_sent_at, email_change_token_new, email_change, email_change_sent_at, last_sign_in_at, raw_app_meta_data, raw_user_meta_data, is_super_admin, created_at, updated_at, phone, phone_confirmed_at, phone_change, phone_change_token, phone_change_sent_at, email_change_token_current, email_change_confirm_status, banned_until, reauthentication_token, reauthentication_sent_at, is_sso_user, deleted_at, is_anonymous) FROM stdin;
\.


--
-- Data for Name: webauthn_challenges; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.webauthn_challenges (id, user_id, challenge_type, session_data, created_at, expires_at) FROM stdin;
\.


--
-- Data for Name: webauthn_credentials; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.webauthn_credentials (id, user_id, credential_id, public_key, attestation_type, aaguid, sign_count, transports, backup_eligible, backed_up, friendly_name, created_at, updated_at, last_used_at) FROM stdin;
\.


--
-- Data for Name: attendance_report_months; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.attendance_report_months (id, school_year_id, month, created_at, updated_at) FROM stdin;
3	6	9	2026-09-14 18:39:38	2026-09-14 18:39:38
\.


--
-- Data for Name: attendance_report_sections; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.attendance_report_sections (id, attendance_report_month_id, grade_level, section, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: audit_logs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.audit_logs (id, user_id, action, module, description, ip_address, created_at, updated_at) FROM stdin;
237	28	created	School Years	Created academic school year 2026-2027	127.0.0.1	2026-09-13 22:00:11	2026-09-13 22:00:11
238	28	updated	School Years	Activated academic school year 2026-2027	127.0.0.1	2026-09-13 22:00:17	2026-09-13 22:00:17
239	28	created	School Years	Created academic school year 2027-2028	127.0.0.1	2026-09-13 22:01:55	2026-09-13 22:01:55
240	27	Updated	Accounts	Updated account profile for Encoder User 1	127.0.0.1	2026-09-13 22:11:25	2026-09-13 22:11:25
241	27	Updated	Accounts	Updated account profile for Encoder User 3	127.0.0.1	2026-09-13 22:12:27	2026-09-13 22:12:27
242	26	Created	Students	Added student Juan Santos	127.0.0.1	2026-09-13 22:45:51	2026-09-13 22:45:51
243	26	Created	Students	Added student Ana Reyes	127.0.0.1	2026-09-13 22:50:53	2026-09-13 22:50:53
244	26	Created	Students	Added student Luis Garcia	127.0.0.1	2026-09-13 22:53:01	2026-09-13 22:53:01
245	26	Created	Students	Added student Bea Mendoza	127.0.0.1	2026-09-13 22:58:33	2026-09-13 22:58:33
246	26	Created	Students	Added student Mark Cruz	127.0.0.1	2026-09-13 23:00:13	2026-09-13 23:00:13
247	26	Created	Students	Added student Lea Bautista	127.0.0.1	2026-09-13 23:01:31	2026-09-13 23:01:31
248	26	Created	Students	Added student Paul Torres	127.0.0.1	2026-09-13 23:07:20	2026-09-13 23:07:20
249	26	Created	Students	Added student Kim Villanueva	127.0.0.1	2026-09-13 23:08:53	2026-09-13 23:08:53
250	26	Created	Students	Added student Ian Ramos	127.0.0.1	2026-09-13 23:10:09	2026-09-13 23:10:09
251	26	Created	Students	Added student Mia Castro	127.0.0.1	2026-09-13 23:11:28	2026-09-13 23:11:28
252	31	Created	Students	Added student Carlos Dizon	127.0.0.1	2026-09-13 23:21:28	2026-09-13 23:21:28
253	31	Created	Students	Added student Rosa Pineda	127.0.0.1	2026-09-13 23:22:44	2026-09-13 23:22:44
254	31	Created	Students	Added student Roy Gutierrez	127.0.0.1	2026-09-13 23:24:46	2026-09-13 23:24:46
255	31	Created	Students	Added student Eve Navarro	127.0.0.1	2026-09-13 23:25:52	2026-09-13 23:25:52
256	31	Created	Students	Added student Jay Mercado	127.0.0.1	2026-09-13 23:27:44	2026-09-13 23:27:44
257	31	Created	Students	Added student Zoe Rivera	127.0.0.1	2026-09-13 23:29:12	2026-09-13 23:29:12
258	31	Created	Students	Added student Leo Ocampo	127.0.0.1	2026-09-13 23:30:40	2026-09-13 23:30:40
259	31	Created	Students	Added student May Aguilar	127.0.0.1	2026-09-13 23:31:44	2026-09-13 23:31:44
260	31	Created	Students	Added student Ken Suarez	127.0.0.1	2026-09-13 23:33:46	2026-09-13 23:33:46
261	31	Created	Students	Added student Joy Ferrer	127.0.0.1	2026-09-13 23:35:15	2026-09-13 23:35:15
262	32	Created	Students	Added student Sam David	127.0.0.1	2026-09-13 23:40:05	2026-09-13 23:40:05
263	32	Created	Students	Added student Ivy Lopez	127.0.0.1	2026-09-13 23:41:06	2026-09-13 23:41:06
264	32	Created	Students	Added student Gil Perez	127.0.0.1	2026-09-13 23:42:01	2026-09-13 23:42:01
265	32	Created	Students	Added student Pat Gomez	127.0.0.1	2026-09-13 23:43:13	2026-09-13 23:43:13
266	32	Created	Students	Added student Ray Yabut	127.0.0.1	2026-09-13 23:44:18	2026-09-13 23:44:18
267	32	Created	Students	Added student Ria Sunga	127.0.0.1	2026-09-13 23:45:50	2026-09-13 23:45:50
268	32	Created	Students	Added student Ted Manalo	127.0.0.1	2026-09-13 23:50:24	2026-09-13 23:50:24
269	32	Created	Students	Added student Amy Valdes	127.0.0.1	2026-09-13 23:51:38	2026-09-13 23:51:38
270	32	Created	Students	Added student Dan Lim	127.0.0.1	2026-09-13 23:52:47	2026-09-13 23:52:47
271	32	Created	Students	Added student Fe Tan	127.0.0.1	2026-09-13 23:53:51	2026-09-13 23:53:51
272	33	Created	Students	Added student Al Go	127.0.0.1	2026-09-14 00:01:17	2026-09-14 00:01:17
273	33	Created	Students	Added student Luz Sy	127.0.0.1	2026-09-14 00:02:14	2026-09-14 00:02:14
274	33	Created	Students	Added student Ben Ong	127.0.0.1	2026-09-14 00:03:29	2026-09-14 00:03:29
275	33	Created	Students	Added student Meg Chua	127.0.0.1	2026-09-14 00:04:29	2026-09-14 00:04:29
276	33	Created	Students	Added student Vic Uy	127.0.0.1	2026-09-14 00:05:25	2026-09-14 00:05:25
277	33	Created	Students	Added student Sol Yap	127.0.0.1	2026-09-14 00:06:29	2026-09-14 00:06:29
278	33	Created	Students	Added student Rey Co	127.0.0.1	2026-09-14 00:07:54	2026-09-14 00:07:54
279	33	Created	Students	Added student Paz Lee	127.0.0.1	2026-09-14 00:09:04	2026-09-14 00:09:04
280	33	Created	Students	Added student Tom Ang	127.0.0.1	2026-09-14 00:10:37	2026-09-14 00:10:37
281	33	Created	Students	Added student Mae Dy	127.0.0.1	2026-09-14 00:11:55	2026-09-14 00:11:55
282	27	updated	School Years	Switched viewing context to school year 2027-2028	127.0.0.1	2026-09-14 00:40:38	2026-09-14 00:40:38
283	27	updated	School Years	Switched viewing context to school year 2026-2027	127.0.0.1	2026-09-14 00:40:45	2026-09-14 00:40:45
284	28	updated	School Years	Switched viewing context to school year 2027-2028	127.0.0.1	2026-09-14 00:53:22	2026-09-14 00:53:22
285	27	updated	School Years	Switched viewing context to school year 2027-2028	127.0.0.1	2026-09-14 00:54:55	2026-09-14 00:54:55
286	28	updated	School Years	Activated academic school year 2027-2028	127.0.0.1	2026-09-14 00:55:43	2026-09-14 00:55:43
287	27	Updated	Accounts	Updated account profile for Encoder User 3	127.0.0.1	2026-09-14 01:01:31	2026-09-14 01:01:31
288	27	Updated	Accounts	Updated account profile for Encoder User	127.0.0.1	2026-09-14 01:03:10	2026-09-14 01:03:10
289	27	Updated	Accounts	Updated account profile for Encoder User 2	127.0.0.1	2026-09-14 01:03:25	2026-09-14 01:03:25
290	27	Updated	Accounts	Updated account profile for Encoder User 1	127.0.0.1	2026-09-14 01:03:49	2026-09-14 01:03:49
291	33	updated	School Years	Switched viewing context to school year 2026-2027	127.0.0.1	2026-09-14 08:35:58	2026-09-14 08:35:58
292	28	updated	School Years	Activated academic school year 2026-2027	127.0.0.1	2026-09-14 14:41:55	2026-09-14 14:41:55
293	26	updated	School Years	Switched viewing context to school year 2026-2027	127.0.0.1	2026-09-14 14:47:16	2026-09-14 14:47:16
294	27	updated	School Years	Switched viewing context to school year 2026-2027	127.0.0.1	2026-09-14 14:52:20	2026-09-14 14:52:20
295	27	updated	School Years	Switched viewing context to school year 2027-2028	127.0.0.1	2026-09-14 14:52:27	2026-09-14 14:52:27
296	31	Updated	SBFP Participants	Uploaded 1 profile image(s) for advisory SBFP participants.	127.0.0.1	2026-09-14 16:51:51	2026-09-14 16:51:51
297	31	Updated	SBFP Participants	Uploaded 1 profile image(s) for advisory SBFP participants.	127.0.0.1	2026-09-14 17:06:32	2026-09-14 17:06:32
298	31	Updated	SBFP Participants	Uploaded 1 profile image(s) for advisory SBFP participants.	127.0.0.1	2026-09-14 17:21:55	2026-09-14 17:21:55
299	31	Updated	SBFP Participants	Uploaded 1 profile image(s) for advisory SBFP participants.	127.0.0.1	2026-09-14 17:22:15	2026-09-14 17:22:15
300	31	Updated	SBFP Participants	Uploaded 1 profile image(s) for advisory SBFP participants.	127.0.0.1	2026-09-14 17:25:06	2026-09-14 17:25:06
301	31	Updated	SBFP Participants	Uploaded 1 profile image(s) for advisory SBFP participants.	127.0.0.1	2026-09-14 17:25:27	2026-09-14 17:25:27
302	31	Updated	SBFP Participants	Uploaded 1 profile image(s) for advisory SBFP participants.	127.0.0.1	2026-09-14 17:25:54	2026-09-14 17:25:54
303	26	Updated	SBFP Participants	Uploaded 1 profile image(s) for advisory SBFP participants.	127.0.0.1	2026-09-14 17:52:39	2026-09-14 17:52:39
304	33	Updated	SBFP Participants	Uploaded 1 profile image(s) for advisory SBFP participants.	127.0.0.1	2026-09-14 18:15:24	2026-09-14 18:15:24
305	26	Updated	Students	Updated student Juan Santos	127.0.0.1	2026-09-14 18:31:13	2026-09-14 18:31:13
306	26	Updated	Students	Updated student Ana Reyes	127.0.0.1	2026-09-14 18:32:13	2026-09-14 18:32:13
307	26	Updated	Students	Updated student Luis Garcia	127.0.0.1	2026-09-14 18:32:29	2026-09-14 18:32:29
308	26	Updated	Students	Updated student Bea Mendoza	127.0.0.1	2026-09-14 18:34:42	2026-09-14 18:34:42
309	26	Updated	Students	Updated student Ana Reyes	127.0.0.1	2026-09-14 18:35:10	2026-09-14 18:35:10
310	26	Updated	Students	Updated student Luis Garcia	127.0.0.1	2026-09-14 18:35:26	2026-09-14 18:35:26
311	26	Updated	Students	Updated student Mark Cruz	127.0.0.1	2026-09-14 18:35:44	2026-09-14 18:35:44
312	27	Created	Meal Plan	Added meal for 2026-09-14: Chicken Adobo	127.0.0.1	2026-09-14 18:38:10	2026-09-14 18:38:10
313	26	Created	Attendance	Scanned QR attendance for student Juan Santos	127.0.0.1	2026-09-14 18:39:41	2026-09-14 18:39:41
314	26	Created	Attendance	Scanned QR attendance for student Luis Garcia	127.0.0.1	2026-09-14 18:39:56	2026-09-14 18:39:56
315	26	Created	Attendance	Scanned QR attendance for student Mark Cruz	127.0.0.1	2026-09-14 18:40:09	2026-09-14 18:40:09
316	31	Updated	Students	Updated student Carlos Dizon	127.0.0.1	2026-09-14 18:41:20	2026-09-14 18:41:20
317	31	Updated	Students	Updated student Rosa Pineda	127.0.0.1	2026-09-14 18:41:42	2026-09-14 18:41:42
318	31	Updated	Students	Updated student Roy Gutierrez	127.0.0.1	2026-09-14 18:42:01	2026-09-14 18:42:01
319	31	Updated	Students	Updated student Eve Navarro	127.0.0.1	2026-09-14 18:42:17	2026-09-14 18:42:17
320	31	Updated	Students	Updated student Jay Mercado	127.0.0.1	2026-09-14 18:42:31	2026-09-14 18:42:31
321	31	Created	Attendance	Scanned QR attendance for student Carlos Dizon	127.0.0.1	2026-09-14 18:43:29	2026-09-14 18:43:29
322	31	Created	Attendance	Scanned QR attendance for student Rosa Pineda	127.0.0.1	2026-09-14 18:43:41	2026-09-14 18:43:41
323	31	Created	Attendance	Scanned QR attendance for student Roy Gutierrez	127.0.0.1	2026-09-14 18:43:51	2026-09-14 18:43:51
324	31	Created	Attendance	Scanned QR attendance for student Jay Mercado	127.0.0.1	2026-09-14 18:44:05	2026-09-14 18:44:05
325	32	Updated	Students	Updated student Sam David	127.0.0.1	2026-09-14 18:45:56	2026-09-14 18:45:56
326	32	Updated	Students	Updated student Ivy Lopez	127.0.0.1	2026-09-14 18:46:12	2026-09-14 18:46:12
327	32	Updated	Students	Updated student Gil Perez	127.0.0.1	2026-09-14 18:46:25	2026-09-14 18:46:25
328	32	Updated	Students	Updated student Pat Gomez	127.0.0.1	2026-09-14 18:46:39	2026-09-14 18:46:39
329	32	Updated	Students	Updated student Ray Yabut	127.0.0.1	2026-09-14 18:46:54	2026-09-14 18:46:54
330	32	Created	Attendance	Scanned QR attendance for student Sam David	127.0.0.1	2026-09-14 18:47:43	2026-09-14 18:47:43
331	32	Created	Attendance	Scanned QR attendance for student Ivy Lopez	127.0.0.1	2026-09-14 18:47:51	2026-09-14 18:47:51
332	33	Updated	Students	Updated student Al Go	127.0.0.1	2026-09-14 18:48:50	2026-09-14 18:48:50
333	33	Updated	Students	Updated student Luz Sy	127.0.0.1	2026-09-14 18:49:06	2026-09-14 18:49:06
334	33	Updated	Students	Updated student Ben Ong	127.0.0.1	2026-09-14 18:49:25	2026-09-14 18:49:25
335	33	Updated	Students	Updated student Meg Chua	127.0.0.1	2026-09-14 18:49:51	2026-09-14 18:49:51
336	33	Updated	Students	Updated student Vic Uy	127.0.0.1	2026-09-14 18:50:04	2026-09-14 18:50:04
337	33	Created	Attendance	Scanned QR attendance for student Al Go	127.0.0.1	2026-09-14 18:51:06	2026-09-14 18:51:06
338	33	Created	Attendance	Scanned QR attendance for student Luz Sy	127.0.0.1	2026-09-14 18:51:16	2026-09-14 18:51:16
339	33	Created	Attendance	Scanned QR attendance for student Ben Ong	127.0.0.1	2026-09-14 18:51:25	2026-09-14 18:51:25
340	33	Created	Attendance	Scanned QR attendance for student Meg Chua	127.0.0.1	2026-09-14 18:51:31	2026-09-14 18:51:31
341	33	Created	Attendance	Scanned QR attendance for student Vic Uy	127.0.0.1	2026-09-14 18:51:40	2026-09-14 18:51:40
342	33	Updated	Attendance	Updated attendance status for participant ID 90 on 2026-09-14	127.0.0.1	2026-09-14 18:52:40	2026-09-14 18:52:40
343	33	Updated	Attendance	Updated attendance status for participant ID 91 on 2026-09-14	127.0.0.1	2026-09-14 18:52:50	2026-09-14 18:52:50
344	27	updated	School Years	Switched viewing context to school year 2027-2028	127.0.0.1	2026-09-15 17:06:07	2026-09-15 17:06:07
345	27	updated	School Years	Switched viewing context to school year 2026-2027	127.0.0.1	2026-09-15 17:06:13	2026-09-15 17:06:13
346	27	updated	School Years	Switched viewing context to school year 2027-2028	127.0.0.1	2026-09-15 17:28:55	2026-09-15 17:28:55
347	27	updated	School Years	Switched viewing context to school year 2026-2027	127.0.0.1	2026-09-15 17:34:29	2026-09-15 17:34:29
348	28	updated	School Years	Activated academic school year 2027-2028	127.0.0.1	2026-09-15 21:49:33	2026-09-15 21:49:33
349	28	updated	School Years	Activated academic school year 2026-2027	127.0.0.1	2026-09-15 21:50:14	2026-09-15 21:50:14
350	27	updated	School Years	Switched viewing context to school year 2027-2028	127.0.0.1	2026-09-15 22:19:45	2026-09-15 22:19:45
351	27	updated	School Years	Switched viewing context to school year 2026-2027	127.0.0.1	2026-09-15 22:19:50	2026-09-15 22:19:50
352	27	updated	School Years	Switched viewing context to school year 2027-2028	127.0.0.1	2026-09-15 22:19:57	2026-09-15 22:19:57
353	27	updated	School Years	Switched viewing context to school year 2026-2027	127.0.0.1	2026-09-15 22:20:09	2026-09-15 22:20:09
354	26	Updated	Attendance	Updated attendance status for participant ID 57 on 2026-09-13	127.0.0.1	2026-09-15 22:46:10	2026-09-15 22:46:10
355	26	Updated	Attendance	Updated attendance status for participant ID 57 on 2026-09-14	127.0.0.1	2026-09-15 22:52:29	2026-09-15 22:52:29
356	26	Updated	Attendance	Updated attendance status for participant ID 57 on 2026-09-13	127.0.0.1	2026-09-15 22:52:36	2026-09-15 22:52:36
357	26	Updated	Attendance	Updated attendance status for participant ID 57 on 2026-09-13	127.0.0.1	2026-09-15 23:13:37	2026-09-15 23:13:37
358	27	updated	School Years	Switched viewing context to school year 2027-2028	127.0.0.1	2026-09-16 09:49:36	2026-09-16 09:49:36
359	27	updated	School Years	Switched viewing context to school year 2026-2027	127.0.0.1	2026-09-16 09:51:28	2026-09-16 09:51:28
360	27	Created	Meal Plan	Added meal for 2026-09-16: Siomai Rice	127.0.0.1	2026-09-16 10:37:39	2026-09-16 10:37:39
361	26	Created	Attendance	Scanned QR attendance for student Juan Santos	127.0.0.1	2026-09-16 10:37:54	2026-09-16 10:37:54
475	33	Created	Students	Added student Chloe Ramos	127.0.0.1	2026-09-20 16:47:32	2026-09-20 16:47:32
362	26	Created	Attendance	Scanned QR attendance for student Luis Garcia	127.0.0.1	2026-09-16 10:38:37	2026-09-16 10:38:37
363	26	Created	Attendance	Scanned QR attendance for student Mark Cruz	127.0.0.1	2026-09-16 10:38:51	2026-09-16 10:38:51
364	26	Updated	Attendance	Updated attendance status for participant ID 59 on 2026-09-16	127.0.0.1	2026-09-16 10:39:20	2026-09-16 10:39:20
365	26	Updated	Attendance	Updated attendance status for participant ID 59 on 2026-09-16	127.0.0.1	2026-09-16 10:39:23	2026-09-16 10:39:23
366	26	updated	School Years	Switched viewing context to school year 2027-2028	127.0.0.1	2026-09-16 10:49:25	2026-09-16 10:49:25
367	26	updated	School Years	Switched viewing context to school year 2026-2027	127.0.0.1	2026-09-16 10:49:29	2026-09-16 10:49:29
368	26	Updated	Students	Renamed advisory section from A to B for 10 student enrollment(s).	127.0.0.1	2026-09-16 10:51:38	2026-09-16 10:51:38
369	26	Updated	Students	Updated student Mia Castro	127.0.0.1	2026-09-16 11:01:28	2026-09-16 11:01:28
370	26	Updated	Students	Renamed advisory section from B to A for 9 student enrollment(s).	127.0.0.1	2026-09-16 11:02:35	2026-09-16 11:02:35
371	26	Updated	SBFP Participants	Uploaded 1 profile image(s) for advisory SBFP participants.	127.0.0.1	2026-09-16 11:08:42	2026-09-16 11:08:42
372	26	Updated	Attendance	Updated attendance status for participant ID 57 on 2026-09-16	127.0.0.1	2026-09-16 11:11:19	2026-09-16 11:11:19
373	26	Updated	Attendance	Updated attendance status for participant ID 59 on 2026-09-16	127.0.0.1	2026-09-16 11:11:22	2026-09-16 11:11:22
374	26	Updated	Attendance	Updated attendance status for participant ID 61 on 2026-09-16	127.0.0.1	2026-09-16 11:11:25	2026-09-16 11:11:25
375	26	Updated	Attendance	Updated attendance status for participant ID 60 on 2026-09-16	127.0.0.1	2026-09-16 11:11:49	2026-09-16 11:11:49
376	26	Updated	Attendance	Updated attendance status for participant ID 60 on 2026-09-16	127.0.0.1	2026-09-16 11:11:54	2026-09-16 11:11:54
377	26	Updated	Attendance	Updated attendance status for participant ID 60 on 2026-09-16	127.0.0.1	2026-09-16 11:11:54	2026-09-16 11:11:54
378	26	Updated	Attendance	Updated attendance status for participant ID 60 on 2026-09-16	127.0.0.1	2026-09-16 11:11:57	2026-09-16 11:11:57
379	26	Updated	Attendance	Updated attendance status for participant ID 60 on 2026-09-16	127.0.0.1	2026-09-16 11:12:00	2026-09-16 11:12:00
380	26	Updated	Attendance	Updated attendance status for participant ID 61 on 2026-09-16	127.0.0.1	2026-09-16 11:12:14	2026-09-16 11:12:14
381	26	Created	Students	Added student chin lee	127.0.0.1	2026-09-16 11:16:03	2026-09-16 11:16:03
382	27	updated	School Years	Switched viewing context to school year 2027-2028	127.0.0.1	2026-09-16 11:21:44	2026-09-16 11:21:44
383	27	updated	School Years	Switched viewing context to school year 2026-2027	127.0.0.1	2026-09-16 11:21:54	2026-09-16 11:21:54
384	27	Updated	Meal Plan	Updated meal plan ID 14	127.0.0.1	2026-09-16 11:22:19	2026-09-16 11:22:19
385	27	Created	Meal Plan	Added meal for 2026-09-16: drinks	127.0.0.1	2026-09-16 11:22:28	2026-09-16 11:22:28
386	27	Created	Meal Plan	Added meal for 2026-09-17: milk	127.0.0.1	2026-09-16 11:22:40	2026-09-16 11:22:40
387	26	Updated	SBFP Participants	Uploaded 1 profile image(s) for advisory SBFP participants.	127.0.0.1	2026-09-16 11:32:43	2026-09-16 11:32:43
388	26	Created	Students	Added student Richard Santos	127.0.0.1	2026-09-16 13:29:36	2026-09-16 13:29:36
389	26	Updated	Students	Updated student chin lee	127.0.0.1	2026-09-16 14:00:58	2026-09-16 14:00:58
390	27	Created	Attendance	Scanned QR attendance for student chin lee	127.0.0.1	2026-09-16 14:03:29	2026-09-16 14:03:29
391	27	Created	Meal Plan	Added meal for 2026-09-18: adobo	127.0.0.1	2026-09-16 14:11:51	2026-09-16 14:11:51
392	27	Created	Meal Plan	Added meal for 2026-09-18: milk	127.0.0.1	2026-09-16 14:11:56	2026-09-16 14:11:56
393	28	Removed	Accounts	Deactivated user account Admin User	127.0.0.1	2026-09-16 14:13:29	2026-09-16 14:13:29
394	28	Restored	Accounts	Restored user account Admin User	127.0.0.1	2026-09-16 14:13:38	2026-09-16 14:13:38
395	26	updated	School Years	Switched viewing context to school year 2027-2028	127.0.0.1	2026-09-17 13:21:00	2026-09-17 13:21:00
396	26	updated	School Years	Switched viewing context to school year 2026-2027	127.0.0.1	2026-09-17 13:21:05	2026-09-17 13:21:05
397	26	Updated	Attendance	Updated attendance status for participant ID 57 on 2026-09-16	127.0.0.1	2026-09-17 13:47:31	2026-09-17 13:47:31
398	27	Updated	Attendance	Updated attendance status for participant ID 90 on 2026-09-17	127.0.0.1	2026-09-18 14:22:28	2026-09-18 14:22:28
399	26	Updated	Attendance	Updated attendance status for participant ID 97 on 2026-09-18	127.0.0.1	2026-09-17 15:10:59	2026-09-17 15:10:59
400	27	Updated	Attendance	Updated attendance status for participant ID 78 on 2026-09-19	127.0.0.1	2026-09-17 15:12:48	2026-09-17 15:12:48
401	27	Updated	Attendance	Updated attendance status for participant ID 57 on 2026-09-13	127.0.0.1	2026-09-17 15:18:52	2026-09-17 15:18:52
402	27	Updated	Attendance	Updated attendance status for participant ID 98 on 2026-09-17	127.0.0.1	2026-09-17 15:20:45	2026-09-17 15:20:45
403	27	Updated	Attendance	Updated attendance status for participant ID 98 on 2026-09-17	127.0.0.1	2026-09-17 15:23:32	2026-09-17 15:23:32
404	27	Updated	Attendance	Updated attendance status for participant ID 91 on 2026-09-17	127.0.0.1	2026-09-17 15:23:47	2026-09-17 15:23:47
405	27	Updated	Attendance	Updated attendance status for participant ID 78 on 2026-09-17	127.0.0.1	2026-09-17 15:23:49	2026-09-17 15:23:49
406	27	Updated	Attendance	Updated attendance status for participant ID 57 on 2026-09-17	127.0.0.1	2026-09-17 15:23:50	2026-09-17 15:23:50
407	27	Updated	Attendance	Updated attendance status for participant ID 61 on 2026-09-17	127.0.0.1	2026-09-17 15:23:51	2026-09-17 15:23:51
408	27	Updated	Attendance	Updated attendance status for participant ID 78 on 2026-09-19	127.0.0.1	2026-09-22 15:26:43	2026-09-22 15:26:43
409	27	Updated	Attendance	Updated attendance status for participant ID 98 on 2026-09-17	127.0.0.1	2026-09-17 15:33:24	2026-09-17 15:33:24
410	27	Updated	Attendance	Updated attendance status for participant ID 98 on 2026-09-17	127.0.0.1	2026-09-17 15:33:34	2026-09-17 15:33:34
411	27	Updated	Attendance	Updated attendance status for participant ID 98 on 2026-09-17	127.0.0.1	2026-09-17 15:33:43	2026-09-17 15:33:43
412	26	Updated	Attendance	Updated attendance status for participant ID 60 on 2026-09-17	127.0.0.1	2026-09-17 15:34:40	2026-09-17 15:34:40
413	26	Updated	Attendance	Updated attendance status for participant ID 98 on 2026-09-17	127.0.0.1	2026-09-17 15:35:05	2026-09-17 15:35:05
414	27	Updated	Attendance	Updated attendance status for participant ID 98 on 2026-09-17	127.0.0.1	2026-09-17 15:36:14	2026-09-17 15:36:14
415	27	Updated	Attendance	Updated attendance status for participant ID 98 on 2026-09-14	127.0.0.1	2026-09-17 16:11:22	2026-09-17 16:11:22
416	27	Updated	Attendance	Updated attendance status for participant ID 98 on 2026-09-16	127.0.0.1	2026-09-17 16:11:36	2026-09-17 16:11:36
417	27	Updated	Attendance	Updated attendance status for participant ID 98 on 2026-09-18	127.0.0.1	2026-09-17 16:11:52	2026-09-17 16:11:52
418	28	Updated	System Settings	Updated the school logo used in downloadable SBFP reports	127.0.0.1	2026-09-17 16:28:01	2026-09-17 16:28:01
419	28	Updated	System Settings	Updated the school logo used in downloadable SBFP reports	127.0.0.1	2026-09-17 17:26:59	2026-09-17 17:26:59
420	28	Updated	System Settings	Updated the school logo used in downloadable SBFP reports	127.0.0.1	2026-09-17 17:40:57	2026-09-17 17:40:57
421	28	Updated	System Settings	Updated the school logo used in downloadable SBFP reports	127.0.0.1	2026-09-17 17:48:01	2026-09-17 17:48:01
422	28	Updated	System Settings	Updated the school logo used in downloadable SBFP reports	127.0.0.1	2026-09-17 18:08:00	2026-09-17 18:08:00
423	28	Updated	System Settings	Updated the school logo used in downloadable SBFP reports	127.0.0.1	2026-09-17 18:12:07	2026-09-17 18:12:07
424	28	Updated	System Settings	Updated the school logo used in downloadable SBFP reports	127.0.0.1	2026-09-17 18:41:21	2026-09-17 18:41:21
425	28	Updated	System Settings	Updated the school logo used in downloadable SBFP reports	127.0.0.1	2026-09-17 18:41:34	2026-09-17 18:41:34
426	28	Updated	System Settings	Updated the DepEd logo used in downloadable SBFP reports	127.0.0.1	2026-09-17 18:43:17	2026-09-17 18:43:17
427	28	Updated	System Settings	Updated the school logo used in downloadable SBFP reports	127.0.0.1	2026-09-17 18:43:27	2026-09-17 18:43:27
428	28	Updated	System Settings	Reset the DepEd report logo to the default	127.0.0.1	2026-09-17 18:44:46	2026-09-17 18:44:46
429	28	Updated	System Settings	Updated the DepEd logo used in downloadable SBFP reports	127.0.0.1	2026-09-17 18:44:53	2026-09-17 18:44:53
430	28	Updated	System Settings	Updated the school logo used in downloadable SBFP reports	127.0.0.1	2026-09-17 18:45:15	2026-09-17 18:45:15
431	28	Updated	System Settings	Updated the school logo used in downloadable SBFP reports	127.0.0.1	2026-09-17 18:46:46	2026-09-17 18:46:46
432	28	Updated	System Settings	Updated the school logo used in downloadable SBFP reports	127.0.0.1	2026-09-17 18:51:03	2026-09-17 18:51:03
433	28	Updated	System Settings	Updated the school logo used in downloadable SBFP reports	127.0.0.1	2026-09-17 18:51:38	2026-09-17 18:51:38
434	28	Updated	System Settings	Updated the school logo used in downloadable SBFP reports	127.0.0.1	2026-09-17 18:55:20	2026-09-17 18:55:20
435	28	Updated	System Settings	Updated the school logo used in downloadable SBFP reports	127.0.0.1	2026-09-17 18:55:56	2026-09-17 18:55:56
436	28	Updated	System Settings	Updated the DepEd logo used in downloadable SBFP reports	127.0.0.1	2026-09-17 18:56:03	2026-09-17 18:56:03
437	28	Updated	System Settings	Reset the DepEd report logo to the default	127.0.0.1	2026-09-17 18:59:23	2026-09-17 18:59:23
438	28	Updated	System Settings	Reset the school report logo to the default	127.0.0.1	2026-09-17 18:59:25	2026-09-17 18:59:25
439	28	Updated	System Settings	Reset the school report logo to the default	127.0.0.1	2026-09-17 19:25:24	2026-09-17 19:25:24
440	28	Updated	System Settings	Reset the DepEd report logo to the default	127.0.0.1	2026-09-17 19:25:28	2026-09-17 19:25:28
441	27	updated	School Years	Switched viewing context to school year 2027-2028	127.0.0.1	2026-09-18 07:52:51	2026-09-18 07:52:51
442	27	updated	School Years	Switched viewing context to school year 2026-2027	127.0.0.1	2026-09-18 07:52:55	2026-09-18 07:52:55
443	27	Updated	Attendance	Updated attendance status for participant ID 98 on 2026-09-18	127.0.0.1	2026-09-18 07:54:30	2026-09-18 07:54:30
444	27	Updated	Account Settings	Updated account profile information	127.0.0.1	2026-09-18 08:01:23	2026-09-18 08:01:23
445	27	Created	Meal Plan	Added meal for 2026-10-01: Crispy pata	127.0.0.1	2026-09-18 08:01:40	2026-09-18 08:01:40
446	26	Created	Students	Added student Janrey Nicdao	127.0.0.1	2026-09-18 10:46:29	2026-09-18 10:46:29
447	27	Created	Meal Plan	Added meal for 2026-09-19: hotdog	127.0.0.1	2026-09-18 10:52:46	2026-09-18 10:52:46
448	27	Created	Attendance	Scanned QR attendance for student Janrey Nicdao	127.0.0.1	2026-09-18 10:54:48	2026-09-18 10:54:48
449	28	updated	School Years	Activated academic school year 2027-2028	127.0.0.1	2026-09-18 11:01:15	2026-09-18 11:01:15
450	28	updated	School Years	Activated academic school year 2026-2027	127.0.0.1	2026-09-18 11:01:27	2026-09-18 11:01:27
451	28	created	School Years	Created academic school year 2028-2029	127.0.0.1	2026-09-18 11:02:11	2026-09-18 11:02:11
452	28	updated	School Years	Activated academic school year 2027-2028	127.0.0.1	2026-09-18 11:02:15	2026-09-18 11:02:15
453	28	updated	School Years	Activated academic school year 2026-2027	127.0.0.1	2026-09-18 11:13:47	2026-09-18 11:13:47
454	26	Created	Students	Added student Matthew Magtoto	127.0.0.1	2026-09-18 13:44:08	2026-09-18 13:44:08
455	27	Created	Attendance	Scanned QR attendance for student Matthew Magtoto	127.0.0.1	2026-09-18 13:50:13	2026-09-18 13:50:13
456	28	updated	School Years	Activated academic school year 2028-2029	127.0.0.1	2026-09-18 14:02:25	2026-09-18 14:02:25
457	28	updated	School Years	Activated academic school year 2026-2027	127.0.0.1	2026-09-18 14:02:48	2026-09-18 14:02:48
458	26	Created	Students	Added student Richard Mungcal	127.0.0.1	2026-09-18 15:59:54	2026-09-18 15:59:54
459	27	Created	Attendance	Scanned QR attendance for student Richard Mungcal	127.0.0.1	2026-09-18 16:05:54	2026-09-18 16:05:54
460	28	Updated	System Settings	Reset the school report logo to the default	127.0.0.1	2026-09-20 15:02:32	2026-09-20 15:02:32
461	26	Updated	SBFP Participants	Uploaded 1 profile image(s) for advisory SBFP participants.	127.0.0.1	2026-09-20 15:41:52	2026-09-20 15:41:52
462	27	Created	Meal Plan	Added meal for 2026-09-20: Siomai Rice	127.0.0.1	2026-09-20 16:26:04	2026-09-20 16:26:04
463	27	Created	Attendance	Scanned QR attendance for student Juan Santos	127.0.0.1	2026-09-20 16:26:16	2026-09-20 16:26:16
464	27	Created	Attendance	Scanned QR attendance for student Bea Mendoza	127.0.0.1	2026-09-20 16:29:59	2026-09-20 16:29:59
465	33	Created	Students	Added student Juan Santos	127.0.0.1	2026-09-20 16:34:06	2026-09-20 16:34:06
466	33	Created	Students	Added student Angela Dela Cruz	127.0.0.1	2026-09-20 16:36:18	2026-09-20 16:36:18
467	33	Created	Students	Added student Daniel Garcia	127.0.0.1	2026-09-20 16:38:34	2026-09-20 16:38:34
468	26	Updated	SBFP Participants	Uploaded 1 profile image(s) for advisory SBFP participants.	127.0.0.1	2026-09-20 16:39:50	2026-09-20 16:39:50
469	33	Created	Students	Added student Sofia Reyes	127.0.0.1	2026-09-20 16:39:52	2026-09-20 16:39:52
470	33	Created	Students	Added student Carlo Mendoza	127.0.0.1	2026-09-20 16:40:58	2026-09-20 16:40:58
471	33	Created	Students	Added student Mia Aquino	127.0.0.1	2026-09-20 16:42:03	2026-09-20 16:42:03
472	33	Created	Students	Added student LIam Navarro	127.0.0.1	2026-09-20 16:43:18	2026-09-20 16:43:18
473	33	Created	Students	Added student Ella Florez	127.0.0.1	2026-09-20 16:44:20	2026-09-20 16:44:20
474	33	Created	Students	Added student Noah Castillo	127.0.0.1	2026-09-20 16:45:37	2026-09-20 16:45:37
476	26	Created	Students	Added student Cacalde Nicdao	127.0.0.1	2026-09-20 17:35:44	2026-09-20 17:35:44
477	26	Updated	SBFP Participants	Uploaded 1 profile image(s) for advisory SBFP participants.	127.0.0.1	2026-09-20 17:59:53	2026-09-20 17:59:53
478	26	Updated	SBFP Participants	Uploaded 1 profile image(s) for advisory SBFP participants.	127.0.0.1	2026-09-20 18:02:39	2026-09-20 18:02:39
479	27	Updated	Accounts	Updated account profile for Encoder U. 1	127.0.0.1	2026-09-20 19:39:40	2026-09-20 19:39:40
480	26	Created	Students	Added student Juan Tamad	127.0.0.1	2026-09-20 19:55:46	2026-09-20 19:55:46
481	33	Created	Students	Added student Chad Santos	127.0.0.1	2026-09-20 20:05:18	2026-09-20 20:05:18
482	33	Updated	Students	Updated student Chad Santos	127.0.0.1	2026-09-20 20:05:39	2026-09-20 20:05:39
483	26	Created	Students	Added student Maria Masipag	127.0.0.1	2026-09-20 20:17:12	2026-09-20 20:17:12
484	33	Updated	Attendance	Updated attendance for Chad Santos on 2026-09-20 to present.	127.0.0.1	2026-09-20 20:22:58	2026-09-20 20:22:58
485	33	Updated	Attendance	Updated attendance for Chad Santos on 2026-09-20 to present.	127.0.0.1	2026-09-20 20:23:04	2026-09-20 20:23:04
486	26	Updated	SBFP Approval	Bulk parent consent changes: Maria Masipag: pending to approved	127.0.0.1	2026-09-20 20:27:18	2026-09-20 20:27:18
487	33	Created	Students	Added student Chad. 2 Santos	127.0.0.1	2026-09-20 20:27:25	2026-09-20 20:27:25
488	33	updated	School Years	Switched viewing context to school year 2027-2028	127.0.0.1	2026-09-20 21:34:57	2026-09-20 21:34:57
489	33	updated	School Years	Switched viewing context to school year 2028-2029	127.0.0.1	2026-09-20 21:36:03	2026-09-20 21:36:03
490	33	updated	School Years	Switched viewing context to school year 2026-2027	127.0.0.1	2026-09-20 21:36:09	2026-09-20 21:36:09
491	33	Updated	Attendance	Updated attendance for Chad Santos on 2026-09-20 to unmarked.	127.0.0.1	2026-09-20 21:55:27	2026-09-20 21:55:27
492	27	Updated	Accounts	Updated account profile for Encoder User	127.0.0.1	2026-09-20 21:55:54	2026-09-20 21:55:54
493	27	Updated	Accounts	Updated account profile for Encoder User	127.0.0.1	2026-09-20 22:01:36	2026-09-20 22:01:36
494	27	Updated	Accounts	Updated account profile for Encoder User	127.0.0.1	2026-09-20 22:03:08	2026-09-20 22:03:08
495	27	Updated	Accounts	Updated account profile for Encoder User	127.0.0.1	2026-09-20 22:03:26	2026-09-20 22:03:26
496	28	Updated	Accounts	Updated account profile for Admin User	127.0.0.1	2026-09-20 22:06:04	2026-09-20 22:06:04
497	28	Updated	Accounts	Updated account profile for Admin User	127.0.0.1	2026-09-20 22:06:17	2026-09-20 22:06:17
498	27	Updated	Accounts	Updated account profile for Encoder U. One	127.0.0.1	2026-09-20 22:14:27	2026-09-20 22:14:27
499	26	Updated	Students	Marked student as withdrawn: Juan Tamad	127.0.0.1	2026-09-20 22:47:00	2026-09-20 22:47:00
500	26	Updated	Account Settings	Updated account profile information	127.0.0.1	2026-09-20 22:48:46	2026-09-20 22:48:46
501	33	Updated	SBFP Participants	Uploaded profile images for: Chad. 2 Santos.	127.0.0.1	2026-09-20 22:49:42	2026-09-20 22:49:42
502	33	Updated	Attendance	Updated attendance for Meg Chua on 2026-09-20 to present.	127.0.0.1	2026-09-20 22:50:20	2026-09-20 22:50:20
503	33	Updated	Attendance	Updated attendance for Angela Dela Cruz on 2026-09-20 to present.	127.0.0.1	2026-09-20 22:50:24	2026-09-20 22:50:24
504	33	Updated	Attendance	Updated attendance for Daniel Garcia on 2026-09-20 to present.	127.0.0.1	2026-09-20 22:50:27	2026-09-20 22:50:27
505	33	Updated	Attendance	Updated attendance for Al Go on 2026-09-20 to present.	127.0.0.1	2026-09-20 22:50:30	2026-09-20 22:50:30
506	33	Updated	Attendance	Updated attendance for Carlo Mendoza on 2026-09-20 to present.	127.0.0.1	2026-09-20 22:50:33	2026-09-20 22:50:33
507	33	Updated	Attendance	Updated attendance for Ben Ong on 2026-09-20 to present.	127.0.0.1	2026-09-20 22:50:36	2026-09-20 22:50:36
508	33	Updated	Attendance	Updated attendance for Sofia Reyes on 2026-09-20 to present.	127.0.0.1	2026-09-20 22:50:39	2026-09-20 22:50:39
509	33	Updated	Attendance	Updated attendance for Chad Santos on 2026-09-20 to present.	127.0.0.1	2026-09-20 22:50:43	2026-09-20 22:50:43
510	33	Updated	Attendance	Updated attendance for Chad. 2 Santos on 2026-09-20 to present.	127.0.0.1	2026-09-20 22:50:50	2026-09-20 22:50:50
511	33	Updated	Attendance	Updated attendance for Juan Santos on 2026-09-20 to present.	127.0.0.1	2026-09-20 22:51:34	2026-09-20 22:51:34
512	33	Updated	Attendance	Updated attendance for Chad Santos on 2026-09-20 to absent.	127.0.0.1	2026-09-20 22:52:00	2026-09-20 22:52:00
513	33	Updated	Attendance	Updated attendance for Chad. 2 Santos on 2026-09-20 to absent.	127.0.0.1	2026-09-20 22:52:30	2026-09-20 22:52:30
514	33	Updated	Attendance	Updated attendance for Chad Santos on 2026-09-20 to unmarked.	127.0.0.1	2026-09-20 22:53:37	2026-09-20 22:53:37
515	33	Updated	Attendance	Updated attendance for Chad. 2 Santos on 2026-09-20 to unmarked.	127.0.0.1	2026-09-20 22:53:40	2026-09-20 22:53:40
516	33	Updated	Attendance	Updated attendance for Luz Sy on 2026-09-20 to present.	127.0.0.1	2026-09-20 22:53:51	2026-09-20 22:53:51
517	33	Updated	Attendance	Updated attendance for Vic Uy on 2026-09-20 to present.	127.0.0.1	2026-09-20 22:53:55	2026-09-20 22:53:55
518	27	Updated	Accounts	Updated account profile for Encoder U. One	127.0.0.1	2026-09-21 07:55:50	2026-09-21 07:55:50
519	33	updated	School Years	Switched viewing context to school year 2027-2028	127.0.0.1	2026-09-22 18:19:47	2026-09-22 18:19:47
520	33	updated	School Years	Switched viewing context to school year 2026-2027	127.0.0.1	2026-09-22 18:36:09	2026-09-22 18:36:09
521	33	Updated	Students	Renamed advisory section from B to A for 22 student enrollment(s).	127.0.0.1	2026-09-22 18:50:44	2026-09-22 18:50:44
522	33	Updated	Students	Marked student as withdrawn: Chad. 2 Santos	127.0.0.1	2026-09-22 18:57:22	2026-09-22 18:57:22
523	33	Updated	Students	Renamed advisory section from A to B for 22 student enrollment(s).	127.0.0.1	2026-09-22 19:04:15	2026-09-22 19:04:15
524	33	Created	Students	Added student Juan Dela Cruz	127.0.0.1	2026-09-22 19:39:55	2026-09-22 19:39:55
525	33	Updated	Students	Updated student Juan Dela Cruz	127.0.0.1	2026-09-22 19:40:55	2026-09-22 19:40:55
526	33	Updated	SBFP Approval	Bulk parent consent changes: Juan Dela Cruz: pending to approved	127.0.0.1	2026-09-22 19:44:21	2026-09-22 19:44:21
527	33	Updated	SBFP Participants	Uploaded profile images for: Juan Dela Cruz.	127.0.0.1	2026-09-22 19:46:41	2026-09-22 19:46:41
528	27	Created	Meal Plan	Added meal for 2026-09-22: Egg	127.0.0.1	2026-09-22 19:50:40	2026-09-22 19:50:40
529	27	Created	Meal Plan	Added meal for 2026-09-22: Milk	127.0.0.1	2026-09-22 19:50:44	2026-09-22 19:50:44
530	27	Updated	Attendance	Updated attendance for Juan Dela Cruz on 2026-09-22 to present.	127.0.0.1	2026-09-22 20:01:16	2026-09-22 20:01:16
531	27	Updated	Attendance	Updated attendance for Juan Dela Cruz on 2026-09-22 to absent.	127.0.0.1	2026-09-22 20:03:33	2026-09-22 20:03:33
532	27	Updated	Attendance	Updated attendance for Juan Dela Cruz on 2026-09-22 to unmarked.	127.0.0.1	2026-09-22 20:03:59	2026-09-22 20:03:59
533	26	Updated	Students	Updated student Bea Mendoza	127.0.0.1	2026-09-22 23:21:47	2026-09-22 23:21:47
534	26	Updated	SBFP Approval	Bulk parent consent changes: Bea Mendoza: approved to pending	127.0.0.1	2026-09-22 23:30:37	2026-09-22 23:30:37
535	26	Updated	Students	Updated student Bea Mendoza	127.0.0.1	2026-09-22 23:31:45	2026-09-22 23:31:45
536	33	Updated	Students	Marked student as withdrawn: Chad Santos	127.0.0.1	2026-09-23 13:48:59	2026-09-23 13:48:59
537	33	Updated	Students	Marked student as withdrawn: Juan Dela Cruz	127.0.0.1	2026-09-23 13:49:10	2026-09-23 13:49:10
538	33	Created	Students	Added student Mark Santos	127.0.0.1	2026-09-23 13:52:10	2026-09-23 13:52:10
539	33	Updated	Students	Updated student Mark Santos	127.0.0.1	2026-09-23 13:52:26	2026-09-23 13:52:26
540	33	Updated	Students	Updated student Mark Santos	127.0.0.1	2026-09-23 13:53:22	2026-09-23 13:53:22
541	26	Created	Students	Added student John Doe	127.0.0.1	2026-09-25 09:39:18	2026-09-25 09:39:18
542	26	Updated	SBFP Approval	Bulk parent consent changes: John Doe: pending to approved	127.0.0.1	2026-09-25 09:53:45	2026-09-25 09:53:45
543	27	Created	Meal Plan	Added meal for 2026-09-25: adobo	127.0.0.1	2026-09-25 10:01:40	2026-09-25 10:01:40
544	27	Created	Attendance	Scanned QR attendance for student John Doe	127.0.0.1	2026-09-25 10:02:21	2026-09-25 10:02:21
\.


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache (key, value, expiration) FROM stdin;
nutrisight-cache-887309d048beef83ad3eabf2a79a64a389ab1c9f:timer	i:1790091252;	1790091252
nutrisight-cache-887309d048beef83ad3eabf2a79a64a389ab1c9f	i:3;	1790091252
nutrisight-cache-5c785c036466adea360111aa28563bfd556b5fba:timer	i:1790142961;	1790142961
nutrisight-cache-5c785c036466adea360111aa28563bfd556b5fba	i:2;	1790142961
nutrisight-cache-b6692ea5df920cad691c20319a6fffd7a4a766b8:timer	i:1789907413;	1789907413
nutrisight-cache-b6692ea5df920cad691c20319a6fffd7a4a766b8	i:2;	1789907413
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: enrollments; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.enrollments (id, student_id, school_year_id, grade_level, section, status, created_at, updated_at) FROM stdin;
67	67	6	2	B	enrolled	2026-09-13 23:21:28	2026-09-13 23:21:28
68	68	6	2	B	enrolled	2026-09-13 23:22:44	2026-09-13 23:22:44
69	69	6	2	B	enrolled	2026-09-13 23:24:45	2026-09-13 23:24:45
70	70	6	2	B	enrolled	2026-09-13 23:25:52	2026-09-13 23:25:52
71	71	6	2	B	enrolled	2026-09-13 23:27:44	2026-09-13 23:27:44
72	72	6	2	B	enrolled	2026-09-13 23:29:11	2026-09-13 23:29:11
73	73	6	2	B	enrolled	2026-09-13 23:30:40	2026-09-13 23:30:40
74	74	6	2	B	enrolled	2026-09-13 23:31:44	2026-09-13 23:31:44
75	75	6	2	B	enrolled	2026-09-13 23:33:45	2026-09-13 23:33:45
76	76	6	2	B	enrolled	2026-09-13 23:35:15	2026-09-13 23:35:15
77	77	6	3	A	enrolled	2026-09-13 23:40:05	2026-09-13 23:40:05
78	78	6	3	A	enrolled	2026-09-13 23:41:06	2026-09-13 23:41:06
79	79	6	3	A	enrolled	2026-09-13 23:42:01	2026-09-13 23:42:01
80	80	6	3	A	enrolled	2026-09-13 23:43:13	2026-09-13 23:43:13
81	81	6	3	A	enrolled	2026-09-13 23:44:17	2026-09-13 23:44:17
82	82	6	3	A	enrolled	2026-09-13 23:45:50	2026-09-13 23:45:50
83	83	6	3	A	enrolled	2026-09-13 23:50:23	2026-09-13 23:50:23
84	84	6	3	A	enrolled	2026-09-13 23:51:38	2026-09-13 23:51:38
85	85	6	3	A	enrolled	2026-09-13 23:52:47	2026-09-13 23:52:47
86	86	6	3	A	enrolled	2026-09-13 23:53:51	2026-09-13 23:53:51
87	87	6	4	B	enrolled	2026-09-14 00:01:16	2026-09-22 19:04:15
88	88	6	4	B	enrolled	2026-09-14 00:02:14	2026-09-22 19:04:15
89	89	6	4	B	enrolled	2026-09-14 00:03:29	2026-09-22 19:04:15
90	90	6	4	B	enrolled	2026-09-14 00:04:29	2026-09-22 19:04:15
91	91	6	4	B	enrolled	2026-09-14 00:05:25	2026-09-22 19:04:15
92	92	6	4	B	enrolled	2026-09-14 00:06:29	2026-09-22 19:04:15
93	93	6	4	B	enrolled	2026-09-14 00:07:54	2026-09-22 19:04:15
94	94	6	4	B	enrolled	2026-09-14 00:09:04	2026-09-22 19:04:15
95	95	6	4	B	enrolled	2026-09-14 00:10:36	2026-09-22 19:04:15
96	96	6	4	B	enrolled	2026-09-14 00:11:55	2026-09-22 19:04:15
66	66	6	1	A	enrolled	2026-09-13 23:11:28	2026-09-16 11:01:28
57	57	6	1	A	enrolled	2026-09-13 22:45:51	2026-09-16 11:02:35
58	58	6	1	A	enrolled	2026-09-13 22:50:53	2026-09-16 11:02:35
59	59	6	1	A	enrolled	2026-09-13 22:53:00	2026-09-16 11:02:35
60	60	6	1	A	enrolled	2026-09-13 22:58:32	2026-09-16 11:02:35
61	61	6	1	A	enrolled	2026-09-13 23:00:12	2026-09-16 11:02:35
62	62	6	1	A	enrolled	2026-09-13 23:01:31	2026-09-16 11:02:35
63	63	6	1	A	enrolled	2026-09-13 23:07:19	2026-09-16 11:02:35
64	64	6	1	A	enrolled	2026-09-13 23:08:52	2026-09-16 11:02:35
65	65	6	1	A	enrolled	2026-09-13 23:10:09	2026-09-16 11:02:35
97	97	6	1	A	enrolled	2026-09-16 11:16:03	2026-09-16 11:16:03
98	98	6	1	A	enrolled	2026-09-16 13:29:36	2026-09-16 13:29:36
99	99	6	1	A	enrolled	2026-09-18 10:46:29	2026-09-18 10:46:29
100	100	6	1	A	enrolled	2026-09-18 13:44:08	2026-09-18 13:44:08
101	101	6	1	A	enrolled	2026-09-18 15:59:54	2026-09-18 15:59:54
112	112	6	1	A	enrolled	2026-09-20 17:35:41	2026-09-20 17:35:41
115	115	6	1	A	enrolled	2026-09-20 20:17:10	2026-09-20 20:17:10
113	113	6	1	A	withdrawn	2026-09-20 19:55:43	2026-09-20 22:47:00
102	102	6	4	B	enrolled	2026-09-20 16:34:06	2026-09-22 19:04:15
103	103	6	4	B	enrolled	2026-09-20 16:36:18	2026-09-22 19:04:15
104	104	6	4	B	enrolled	2026-09-20 16:38:34	2026-09-22 19:04:15
105	105	6	4	B	enrolled	2026-09-20 16:39:52	2026-09-22 19:04:15
106	106	6	4	B	enrolled	2026-09-20 16:40:58	2026-09-22 19:04:15
107	107	6	4	B	enrolled	2026-09-20 16:42:03	2026-09-22 19:04:15
108	108	6	4	B	enrolled	2026-09-20 16:43:18	2026-09-22 19:04:15
109	109	6	4	B	enrolled	2026-09-20 16:44:20	2026-09-22 19:04:15
110	110	6	4	B	enrolled	2026-09-20 16:45:37	2026-09-22 19:04:15
111	111	6	4	B	enrolled	2026-09-20 16:47:32	2026-09-22 19:04:15
116	116	6	4	B	withdrawn	2026-09-20 20:27:24	2026-09-22 19:04:15
114	114	6	4	B	withdrawn	2026-09-20 20:05:16	2026-09-23 13:48:59
117	117	6	4	B	withdrawn	2026-09-22 19:39:53	2026-09-23 13:49:10
118	118	6	4	B	enrolled	2026-09-23 13:52:09	2026-09-23 13:52:09
119	119	6	1	A	enrolled	2026-09-25 09:39:17	2026-09-25 09:39:17
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
11	default	{"uuid":"ff2689a2-56f8-475a-8bac-8b97452c5d42","displayName":"App\\\\Mail\\\\FeedingDayNotice","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":3,"maxExceptions":null,"failOnTimeout":false,"backoff":"10,60,300","timeout":null,"retryUntil":null,"deleteWhenMissingModels":false,"data":{"commandName":"Illuminate\\\\Mail\\\\SendQueuedMailable","command":"O:34:\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\":19:{s:8:\\"mailable\\";O:25:\\"App\\\\Mail\\\\FeedingDayNotice\\":5:{s:7:\\"student\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:18:\\"App\\\\Models\\\\Student\\";s:2:\\"id\\";i:35;s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"pgsql\\";s:15:\\"collectionClass\\";N;}s:4:\\"meal\\";s:12:\\"siomai, rice\\";s:4:\\"date\\";s:10:\\"2026-09-01\\";s:2:\\"to\\";a:1:{i:0;a:2:{s:4:\\"name\\";N;s:7:\\"address\\";s:17:\\"grace.v@gmail.com\\";}}s:6:\\"mailer\\";s:3:\\"log\\";}s:5:\\"tries\\";i:3;s:7:\\"timeout\\";N;s:13:\\"maxExceptions\\";N;s:17:\\"shouldBeEncrypted\\";b:0;s:3:\\"job\\";N;s:10:\\"connection\\";N;s:5:\\"queue\\";N;s:12:\\"messageGroup\\";N;s:12:\\"deduplicator\\";N;s:13:\\"debounceOwner\\";s:0:\\"\\";s:15:\\"uniqueLockOwner\\";s:0:\\"\\";s:5:\\"delay\\";N;s:11:\\"afterCommit\\";N;s:10:\\"middleware\\";a:0:{}s:7:\\"chained\\";a:0:{}s:15:\\"chainConnection\\";N;s:10:\\"chainQueue\\";N;s:19:\\"chainCatchCallbacks\\";N;}","batchId":null},"createdAt":1789106266,"delay":null}	0	\N	1789106267	1789106267
12	default	{"uuid":"e0e639ac-384c-4807-8382-f4eee4f2213a","displayName":"App\\\\Mail\\\\FeedingDayNotice","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":3,"maxExceptions":null,"failOnTimeout":false,"backoff":"10,60,300","timeout":null,"retryUntil":null,"deleteWhenMissingModels":false,"data":{"commandName":"Illuminate\\\\Mail\\\\SendQueuedMailable","command":"O:34:\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\":19:{s:8:\\"mailable\\";O:25:\\"App\\\\Mail\\\\FeedingDayNotice\\":5:{s:7:\\"student\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:18:\\"App\\\\Models\\\\Student\\";s:2:\\"id\\";i:35;s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"pgsql\\";s:15:\\"collectionClass\\";N;}s:4:\\"meal\\";s:19:\\"Chicken Adobo, Milo\\";s:4:\\"date\\";s:10:\\"2026-09-11\\";s:2:\\"to\\";a:1:{i:0;a:2:{s:4:\\"name\\";N;s:7:\\"address\\";s:17:\\"grace.v@gmail.com\\";}}s:6:\\"mailer\\";s:3:\\"log\\";}s:5:\\"tries\\";i:3;s:7:\\"timeout\\";N;s:13:\\"maxExceptions\\";N;s:17:\\"shouldBeEncrypted\\";b:0;s:3:\\"job\\";N;s:10:\\"connection\\";N;s:5:\\"queue\\";N;s:12:\\"messageGroup\\";N;s:12:\\"deduplicator\\";N;s:13:\\"debounceOwner\\";s:0:\\"\\";s:15:\\"uniqueLockOwner\\";s:0:\\"\\";s:5:\\"delay\\";N;s:11:\\"afterCommit\\";N;s:10:\\"middleware\\";a:0:{}s:7:\\"chained\\";a:0:{}s:15:\\"chainConnection\\";N;s:10:\\"chainQueue\\";N;s:19:\\"chainCatchCallbacks\\";N;}","batchId":null},"createdAt":1789106277,"delay":null}	0	\N	1789106277	1789106277
13	default	{"uuid":"3f31f94e-4289-4d2e-970b-d01d4d6edc2c","displayName":"App\\\\Mail\\\\FeedingDayNotice","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":3,"maxExceptions":null,"failOnTimeout":false,"backoff":"10,60,300","timeout":null,"retryUntil":null,"deleteWhenMissingModels":false,"data":{"commandName":"Illuminate\\\\Mail\\\\SendQueuedMailable","command":"O:34:\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\":19:{s:8:\\"mailable\\";O:25:\\"App\\\\Mail\\\\FeedingDayNotice\\":5:{s:7:\\"student\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:18:\\"App\\\\Models\\\\Student\\";s:2:\\"id\\";i:35;s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"pgsql\\";s:15:\\"collectionClass\\";N;}s:4:\\"meal\\";s:12:\\"siomai, rice\\";s:4:\\"date\\";s:10:\\"2026-09-10\\";s:2:\\"to\\";a:1:{i:0;a:2:{s:4:\\"name\\";N;s:7:\\"address\\";s:17:\\"grace.v@gmail.com\\";}}s:6:\\"mailer\\";s:3:\\"log\\";}s:5:\\"tries\\";i:3;s:7:\\"timeout\\";N;s:13:\\"maxExceptions\\";N;s:17:\\"shouldBeEncrypted\\";b:0;s:3:\\"job\\";N;s:10:\\"connection\\";N;s:5:\\"queue\\";N;s:12:\\"messageGroup\\";N;s:12:\\"deduplicator\\";N;s:13:\\"debounceOwner\\";s:0:\\"\\";s:15:\\"uniqueLockOwner\\";s:0:\\"\\";s:5:\\"delay\\";N;s:11:\\"afterCommit\\";N;s:10:\\"middleware\\";a:0:{}s:7:\\"chained\\";a:0:{}s:15:\\"chainConnection\\";N;s:10:\\"chainQueue\\";N;s:19:\\"chainCatchCallbacks\\";N;}","batchId":null},"createdAt":1789106291,"delay":null}	0	\N	1789106291	1789106291
\.


--
-- Data for Name: meal_plans; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.meal_plans (id, meal_date, meal_name, recorded_by_user_id, created_at, updated_at) FROM stdin;
13	2026-09-14	Chicken Adobo	27	2026-09-14 18:38:10	2026-09-14 18:38:10
14	2026-09-16	Siomai Rice	27	2026-09-16 10:37:39	2026-09-16 10:37:39
15	2026-09-16	drinks	27	2026-09-16 11:22:28	2026-09-16 11:22:28
16	2026-09-17	milk	27	2026-09-16 11:22:40	2026-09-16 11:22:40
17	2026-09-18	adobo	27	2026-09-16 14:11:51	2026-09-16 14:11:51
18	2026-09-18	milk	27	2026-09-16 14:11:56	2026-09-16 14:11:56
19	2026-10-01	Crispy pata	27	2026-09-18 08:01:40	2026-09-18 08:01:40
20	2026-09-19	hotdog	27	2026-09-18 10:52:46	2026-09-18 10:52:46
21	2026-09-20	Siomai Rice	27	2026-09-20 16:26:04	2026-09-20 16:26:04
22	2026-09-22	Egg	27	2026-09-22 19:50:40	2026-09-22 19:50:40
23	2026-09-22	Milk	27	2026-09-22 19:50:44	2026-09-22 19:50:44
24	2026-09-25	adobo	27	2026-09-25 10:01:40	2026-09-25 10:01:40
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.migrations (id, migration, batch) FROM stdin;
24	2026_09_10_000000_update_sbfp_approval_and_measurements	1
25	2026_09_11_000000_add_profile_image_url_to_sbfp_participants_table	2
26	2026_09_11_190000_allow_multiple_report_periods_per_month	3
27	0001_01_01_000000_create_users_table	1
28	0001_01_01_000001_create_cache_table	1
29	0001_01_01_000002_create_jobs_table	1
30	2026_09_05_110358_create_school_years_table	1
31	2026_09_05_110502_create_students_table	1
32	2026_09_05_110709_create_enrollments_table	1
33	2026_09_05_110722_create_sbfp_participants_table	1
34	2026_09_05_110731_create_nutrition_measurements_table	1
35	2026_09_05_110739_create_student_attendance_records_table	1
36	2026_09_05_110746_create_student_feeding_records_table	1
37	2026_09_05_120000_create_audit_logs_table	1
38	2026_09_07_120000_create_report_periods_table	1
39	2026_09_07_120001_create_report_period_rows_table	1
40	2026_09_07_130000_make_school_year_end_date_nullable	1
41	2026_09_07_140000_add_measurement_period_to_report_periods	1
42	2026_09_07_140001_update_report_period_uniqueness	1
43	2026_09_07_150000_add_height_count_to_report_period_rows	1
44	2026_09_07_160000_enforce_unique_report_terms_and_months	1
45	2026_09_07_170000_create_attendance_report_months_table	1
46	2026_09_07_180000_create_attendance_report_sections_table	1
47	2026_09_08_000000_add_attendance_lookup_indexes	1
48	2026_09_09_000001_add_guardian_contact_to_students_table	1
49	2026_09_10_000000_create_meal_plans_table	1
50	2026_09_13_000000_create_school_year_user_records_table	4
51	2026_09_14_000001_add_name_parts_to_users_table	5
52	2026_09_14_000002_sync_legacy_user_names	6
53	2026_09_20_000001_create_sbfp_parent_approval_requests_table	7
\.


--
-- Data for Name: nutrition_measurements; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.nutrition_measurements (id, sbfp_participant_id, height, weight, bmi, bmi_category, hfa, measurement_period, created_at, updated_at) FROM stdin;
81	57	165	40	14.69	Severely Wasted	Normal	baseline	2026-09-13 22:45:51	2026-09-13 22:45:51
82	58	165	40	14.69	Severely Wasted	Normal	baseline	2026-09-13 22:50:53	2026-09-13 22:50:53
83	59	165	40	14.69	Severely Wasted	Normal	baseline	2026-09-13 22:53:00	2026-09-13 22:53:00
86	62	165	58	21.30	Normal	Normal	baseline	2026-09-13 23:01:31	2026-09-13 23:01:31
87	63	165	58	21.30	Normal	Normal	baseline	2026-09-13 23:07:19	2026-09-13 23:07:19
88	64	165	58	21.30	Normal	Normal	baseline	2026-09-13 23:08:53	2026-09-13 23:08:53
89	65	165	72	26.45	Overweight	Normal	baseline	2026-09-13 23:10:09	2026-09-13 23:10:09
90	66	165	85	31.22	Obese	Normal	baseline	2026-09-13 23:11:28	2026-09-13 23:11:28
91	67	165	40	14.69	Severely Wasted	Normal	baseline	2026-09-13 23:21:28	2026-09-13 23:21:28
92	68	165	40	14.69	Severely Wasted	Normal	baseline	2026-09-13 23:22:44	2026-09-13 23:22:44
93	69	165	40	14.69	Severely Wasted	Normal	baseline	2026-09-13 23:24:46	2026-09-13 23:24:46
96	72	165	58	21.30	Normal	Normal	baseline	2026-09-13 23:29:11	2026-09-13 23:29:11
97	73	165	58	21.30	Normal	Normal	baseline	2026-09-13 23:30:40	2026-09-13 23:30:40
98	74	165	58	21.30	Normal	Normal	baseline	2026-09-13 23:31:44	2026-09-13 23:31:44
99	75	165	72	26.45	Overweight	Normal	baseline	2026-09-13 23:33:45	2026-09-13 23:33:45
100	76	165	85	31.22	Obese	Normal	baseline	2026-09-13 23:35:15	2026-09-13 23:35:15
101	77	165	40	14.69	Severely Wasted	Normal	baseline	2026-09-13 23:40:05	2026-09-13 23:40:05
102	78	165	40	14.69	Severely Wasted	Normal	baseline	2026-09-13 23:41:06	2026-09-13 23:41:06
103	79	165	40	14.69	Severely Wasted	Normal	baseline	2026-09-13 23:42:01	2026-09-13 23:42:01
106	82	165	58	21.30	Normal	Normal	baseline	2026-09-13 23:45:50	2026-09-13 23:45:50
107	83	165	58	21.30	Normal	Normal	baseline	2026-09-13 23:50:24	2026-09-13 23:50:24
108	84	165	58	21.30	Normal	Normal	baseline	2026-09-13 23:51:38	2026-09-13 23:51:38
109	85	165	72	26.45	Overweight	Normal	baseline	2026-09-13 23:52:47	2026-09-13 23:52:47
110	86	165	85	31.22	Obese	Normal	baseline	2026-09-13 23:53:51	2026-09-13 23:53:51
111	87	165	40	14.69	Severely Wasted	Normal	baseline	2026-09-14 00:01:16	2026-09-14 00:01:16
112	88	165	40	14.69	Severely Wasted	Normal	baseline	2026-09-14 00:02:14	2026-09-14 00:02:14
113	89	165	40	14.69	Severely Wasted	Normal	baseline	2026-09-14 00:03:29	2026-09-14 00:03:29
116	92	165	58	21.30	Normal	Normal	baseline	2026-09-14 00:06:29	2026-09-14 00:06:29
117	93	165	58	21.30	Normal	Normal	baseline	2026-09-14 00:07:54	2026-09-14 00:07:54
118	94	165	58	21.30	Normal	Normal	baseline	2026-09-14 00:09:04	2026-09-14 00:09:04
119	95	165	72	26.45	Overweight	Normal	baseline	2026-09-14 00:10:37	2026-09-14 00:10:37
120	96	165	85	31.22	Obese	Normal	baseline	2026-09-14 00:11:55	2026-09-14 00:11:55
121	57	165	46	16.90	Wasted	Normal	midline	2026-09-14 00:25:50	2026-09-14 00:25:50
122	58	165	46	16.90	Wasted	Normal	midline	2026-09-14 00:25:51	2026-09-14 00:25:51
123	59	165	46	16.90	Wasted	Normal	midline	2026-09-14 00:25:52	2026-09-14 00:25:52
124	60	165	46	16.90	Wasted	Normal	midline	2026-09-14 00:25:53	2026-09-14 00:25:53
125	61	165	46	16.90	Wasted	Normal	midline	2026-09-14 00:25:53	2026-09-14 00:25:53
127	58	165	58	21.30	Normal	Normal	endline	2026-09-14 00:26:54	2026-09-14 00:26:54
129	60	165	58	21.30	Normal	Normal	endline	2026-09-14 00:26:55	2026-09-14 00:26:55
131	67	165	46	16.90	Wasted	Normal	midline	2026-09-14 00:29:00	2026-09-14 00:29:00
132	68	165	46	16.90	Wasted	Normal	midline	2026-09-14 00:29:00	2026-09-14 00:29:00
133	69	165	46	16.90	Wasted	Normal	midline	2026-09-14 00:29:01	2026-09-14 00:29:01
134	70	165	58	21.30	Normal	Normal	midline	2026-09-14 00:29:01	2026-09-14 00:29:01
135	71	165	58	21.30	Normal	Normal	midline	2026-09-14 00:29:01	2026-09-14 00:29:01
136	67	165	58	21.30	Normal	Normal	endline	2026-09-14 00:30:00	2026-09-14 00:30:00
137	68	165	58	21.30	Normal	Normal	endline	2026-09-14 00:30:01	2026-09-14 00:30:01
138	69	165	58	21.30	Normal	Normal	endline	2026-09-14 00:30:01	2026-09-14 00:30:01
139	70	165	72	26.45	Overweight	Normal	endline	2026-09-14 00:30:02	2026-09-14 00:30:02
140	71	165	72	26.45	Overweight	Normal	endline	2026-09-14 00:30:02	2026-09-14 00:30:02
141	77	165	46	16.90	Wasted	Normal	midline	2026-09-14 00:32:50	2026-09-14 00:32:50
142	78	165	46	16.90	Wasted	Normal	midline	2026-09-14 00:32:51	2026-09-14 00:32:51
143	79	165	46	16.90	Wasted	Normal	midline	2026-09-14 00:32:51	2026-09-14 00:32:51
144	80	165	58	21.30	Normal	Normal	midline	2026-09-14 00:32:52	2026-09-14 00:32:52
145	81	165	58	21.30	Normal	Normal	midline	2026-09-14 00:32:52	2026-09-14 00:32:52
146	77	165	58	21.30	Normal	Normal	endline	2026-09-14 00:33:25	2026-09-14 00:33:25
147	78	165	58	21.30	Normal	Normal	endline	2026-09-14 00:33:25	2026-09-14 00:33:25
148	79	165	58	21.30	Normal	Normal	endline	2026-09-14 00:33:26	2026-09-14 00:33:26
149	80	165	72	26.45	Overweight	Normal	endline	2026-09-14 00:33:26	2026-09-14 00:33:26
150	81	165	72	26.45	Overweight	Normal	endline	2026-09-14 00:33:27	2026-09-14 00:33:27
151	87	165	46	16.90	Wasted	Normal	midline	2026-09-14 00:34:48	2026-09-14 00:34:48
152	88	165	46	16.90	Wasted	Normal	midline	2026-09-14 00:34:48	2026-09-14 00:34:48
153	89	165	46	16.90	Wasted	Normal	midline	2026-09-14 00:34:49	2026-09-14 00:34:49
154	90	165	58	21.30	Normal	Normal	midline	2026-09-14 00:34:49	2026-09-14 00:34:49
85	61	165	46	16.90	Wasted	Normal	baseline	2026-09-13 23:00:12	2026-09-14 18:35:43
94	70	165	46	16.90	Wasted	Normal	baseline	2026-09-13 23:25:52	2026-09-14 18:42:17
95	71	165	46	16.90	Wasted	Normal	baseline	2026-09-13 23:27:44	2026-09-14 18:42:31
104	80	165	46	16.90	Wasted	Normal	baseline	2026-09-13 23:43:13	2026-09-14 18:46:38
105	81	165	46	16.90	Wasted	Normal	baseline	2026-09-13 23:44:17	2026-09-14 18:46:54
114	90	165	46	16.90	Wasted	Normal	baseline	2026-09-14 00:04:29	2026-09-14 18:49:51
115	91	165	46	16.90	Wasted	Normal	baseline	2026-09-14 00:05:25	2026-09-14 18:50:04
126	57	165	1	0.37	Severely Wasted	Normal	endline	2026-09-14 00:26:53	2026-09-15 21:00:29
155	91	165	58	21.30	Normal	Normal	midline	2026-09-14 00:34:50	2026-09-14 00:34:50
156	87	165	58	21.30	Normal	Normal	endline	2026-09-14 00:35:32	2026-09-14 00:36:10
157	88	165	58	21.30	Normal	Normal	endline	2026-09-14 00:35:33	2026-09-14 00:36:11
158	89	165	58	21.30	Normal	Normal	endline	2026-09-14 00:35:33	2026-09-14 00:36:11
159	90	165	72	26.45	Overweight	Normal	endline	2026-09-14 00:35:34	2026-09-14 00:36:12
160	91	165	72	26.45	Overweight	Normal	endline	2026-09-14 00:35:34	2026-09-14 00:36:12
128	59	165	58	21.30	Normal	Normal	endline	2026-09-14 00:26:55	2026-09-15 21:00:30
130	61	165	58	21.30	Normal	Normal	endline	2026-09-14 00:26:56	2026-09-15 21:00:30
161	97	115	18.5	13.99	Severely Wasted	Normal	baseline	2026-09-16 11:16:03	2026-09-16 11:16:03
162	98	165	40	14.69	Severely Wasted	Normal	baseline	2026-09-16 13:29:36	2026-09-16 13:29:36
163	99	165	40	14.69	Severely Wasted	Normal	baseline	2026-09-18 10:46:29	2026-09-18 10:46:29
164	100	165	15	5.51	Severely Wasted	Normal	baseline	2026-09-18 13:44:08	2026-09-18 13:44:08
165	101	160	12	4.69	Severely Wasted	Normal	baseline	2026-09-18 15:59:54	2026-09-18 15:59:54
166	102	165	40	14.69	Severely Wasted	Normal	baseline	2026-09-20 16:34:06	2026-09-20 16:34:06
167	103	165	40	14.69	Severely Wasted	Normal	baseline	2026-09-20 16:36:18	2026-09-20 16:36:18
168	104	165	40	14.69	Severely Wasted	Normal	baseline	2026-09-20 16:38:34	2026-09-20 16:38:34
169	105	165	46	16.90	Wasted	Normal	baseline	2026-09-20 16:39:52	2026-09-20 16:39:52
170	106	165	46	16.90	Wasted	Normal	baseline	2026-09-20 16:40:58	2026-09-20 16:40:58
171	107	165	58	21.30	Normal	Normal	baseline	2026-09-20 16:42:03	2026-09-20 16:42:03
172	108	165	72	26.45	Overweight	Normal	baseline	2026-09-20 16:43:18	2026-09-20 16:43:18
173	109	165	85	31.22	Obese	Normal	baseline	2026-09-20 16:44:20	2026-09-20 16:44:20
174	110	165	58	21.30	Normal	Normal	baseline	2026-09-20 16:45:37	2026-09-20 16:45:37
175	98	165	47	17.26	Wasted	Normal	midline	2026-09-20 16:46:23	2026-09-20 16:46:23
176	97	165	46	16.90	Wasted	Normal	midline	2026-09-20 16:46:23	2026-09-20 16:46:23
177	99	165	47	17.26	Wasted	Normal	midline	2026-09-20 16:46:24	2026-09-20 16:46:24
178	100	165	47	17.26	Wasted	Normal	midline	2026-09-20 16:46:24	2026-09-20 16:46:24
179	101	165	47	17.26	Wasted	Normal	midline	2026-09-20 16:46:25	2026-09-20 16:46:25
180	111	165	58	21.30	Normal	Normal	baseline	2026-09-20 16:47:32	2026-09-20 16:47:32
181	112	165	40	14.69	Severely Wasted	Normal	baseline	2026-09-20 17:35:41	2026-09-20 17:35:41
182	113	165	46	16.90	Wasted	Normal	baseline	2026-09-20 19:55:43	2026-09-20 19:55:43
183	114	165	46	16.90	Wasted	Normal	baseline	2026-09-20 20:05:16	2026-09-20 20:05:39
184	115	165	46	16.90	Wasted	Normal	baseline	2026-09-20 20:17:10	2026-09-20 20:17:10
185	116	165	46	16.90	Wasted	Normal	baseline	2026-09-20 20:27:24	2026-09-20 20:27:24
186	117	231	21	3.94	Severely Wasted	Normal	baseline	2026-09-22 19:39:53	2026-09-22 19:39:53
84	60	165	46	16.90	Wasted	Normal	baseline	2026-09-13 22:58:32	2026-09-22 23:31:43
187	118	165	50	18.37	Wasted	Normal	baseline	2026-09-23 13:52:09	2026-09-23 13:52:25
188	119	165	46	16.90	Wasted	Normal	baseline	2026-09-25 09:39:17	2026-09-25 09:39:17
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
nutrisight1@mailto.plus	$2y$12$slunUG6CFp6kseMDqD1woOP5RSJma4qqnBs/rqGdPxrt.ZAOv8fZ2	2026-09-20 21:56:49
moltenterabyte@gmail.com	$2y$12$oEd/nvXA8ekfKUNJrZkyduAFJaRq6tLmPDgU5QWUvJ6B3ZglCx/Xa	2026-09-21 08:19:28
\.


--
-- Data for Name: report_period_rows; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.report_period_rows (id, report_period_id, grade_level, sex, enrollment, pupils_weighed, bmi_severely_wasted, bmi_wasted, bmi_normal, bmi_overweight, bmi_obese, hfa_severely_stunted, hfa_stunted, hfa_normal, hfa_tall, created_at, updated_at, pupils_height_taken) FROM stdin;
1953	5	0	M	0	0	0	0	0	0	0	0	0	0	0	2026-09-17 19:54:42	2026-09-17 19:54:42	0
1954	5	0	F	0	0	0	0	0	0	0	0	0	0	0	2026-09-17 19:54:42	2026-09-17 19:54:42	0
1955	5	1	M	4	2	0	2	0	0	0	0	0	2	0	2026-09-17 19:54:42	2026-09-17 19:54:42	2
1956	5	1	F	1	1	0	1	0	0	0	0	0	1	0	2026-09-17 19:54:42	2026-09-17 19:54:42	1
1957	5	2	M	3	3	0	2	1	0	0	0	0	3	0	2026-09-17 19:54:42	2026-09-17 19:54:42	3
1958	5	2	F	1	1	0	1	0	0	0	0	0	1	0	2026-09-17 19:54:42	2026-09-17 19:54:42	1
1959	5	3	M	1	1	0	1	0	0	0	0	0	1	0	2026-09-17 19:54:42	2026-09-17 19:54:42	1
1960	5	3	F	1	1	0	1	0	0	0	0	0	1	0	2026-09-17 19:54:42	2026-09-17 19:54:42	1
1961	5	4	M	3	3	0	2	1	0	0	0	0	3	0	2026-09-17 19:54:42	2026-09-17 19:54:42	3
1962	5	4	F	2	2	0	1	1	0	0	0	0	2	0	2026-09-17 19:54:42	2026-09-17 19:54:42	2
1963	5	5	M	0	0	0	0	0	0	0	0	0	0	0	2026-09-17 19:54:42	2026-09-17 19:54:42	0
1964	5	5	F	0	0	0	0	0	0	0	0	0	0	0	2026-09-17 19:54:42	2026-09-17 19:54:42	0
1965	5	6	M	0	0	0	0	0	0	0	0	0	0	0	2026-09-17 19:54:42	2026-09-17 19:54:42	0
1966	5	6	F	0	0	0	0	0	0	0	0	0	0	0	2026-09-17 19:54:42	2026-09-17 19:54:42	0
1967	5	7	M	0	0	0	0	0	0	0	0	0	0	0	2026-09-17 19:54:42	2026-09-17 19:54:42	0
1968	5	7	F	0	0	0	0	0	0	0	0	0	0	0	2026-09-17 19:54:42	2026-09-17 19:54:42	0
2209	4	0	M	0	0	0	0	0	0	0	0	0	0	0	2026-09-25 10:06:15	2026-09-25 10:06:15	0
2210	4	0	F	0	0	0	0	0	0	0	0	0	0	0	2026-09-25 10:06:15	2026-09-25 10:06:15	0
2211	4	1	M	10	10	7	3	0	0	0	0	0	10	0	2026-09-25 10:06:15	2026-09-25 10:06:15	10
2212	4	1	F	2	2	0	2	0	0	0	0	0	2	0	2026-09-25 10:06:15	2026-09-25 10:06:15	2
2213	4	2	M	3	3	2	1	0	0	0	0	0	3	0	2026-09-25 10:06:15	2026-09-25 10:06:15	3
2214	4	2	F	1	1	1	0	0	0	0	0	0	1	0	2026-09-25 10:06:16	2026-09-25 10:06:16	1
2215	4	3	M	1	1	1	0	0	0	0	0	0	1	0	2026-09-25 10:06:16	2026-09-25 10:06:16	1
2216	4	3	F	1	1	1	0	0	0	0	0	0	1	0	2026-09-25 10:06:16	2026-09-25 10:06:16	1
2217	4	4	M	10	10	5	5	0	0	0	0	0	10	0	2026-09-25 10:06:16	2026-09-25 10:06:16	10
2218	4	4	F	4	4	2	2	0	0	0	0	0	4	0	2026-09-25 10:06:16	2026-09-25 10:06:16	4
2219	4	5	M	0	0	0	0	0	0	0	0	0	0	0	2026-09-25 10:06:16	2026-09-25 10:06:16	0
2220	4	5	F	0	0	0	0	0	0	0	0	0	0	0	2026-09-25 10:06:16	2026-09-25 10:06:16	0
2221	4	6	M	0	0	0	0	0	0	0	0	0	0	0	2026-09-25 10:06:16	2026-09-25 10:06:16	0
2222	4	6	F	0	0	0	0	0	0	0	0	0	0	0	2026-09-25 10:06:16	2026-09-25 10:06:16	0
2223	4	7	M	0	0	0	0	0	0	0	0	0	0	0	2026-09-25 10:06:16	2026-09-25 10:06:16	0
2224	4	7	F	0	0	0	0	0	0	0	0	0	0	0	2026-09-25 10:06:16	2026-09-25 10:06:16	0
2081	6	0	M	0	0	0	0	0	0	0	0	0	0	0	2026-09-18 16:08:12	2026-09-18 16:08:12	0
2082	6	0	F	0	0	0	0	0	0	0	0	0	0	0	2026-09-18 16:08:12	2026-09-18 16:08:12	0
2083	6	1	M	7	2	1	0	1	0	0	0	0	2	0	2026-09-18 16:08:12	2026-09-18 16:08:12	2
2084	6	1	F	1	1	0	0	1	0	0	0	0	1	0	2026-09-18 16:08:12	2026-09-18 16:08:12	1
2085	6	2	M	3	3	0	0	2	1	0	0	0	3	0	2026-09-18 16:08:12	2026-09-18 16:08:12	3
2086	6	2	F	1	1	0	0	1	0	0	0	0	1	0	2026-09-18 16:08:12	2026-09-18 16:08:12	1
2087	6	3	M	1	1	0	0	1	0	0	0	0	1	0	2026-09-18 16:08:12	2026-09-18 16:08:12	1
2088	6	3	F	1	1	0	0	1	0	0	0	0	1	0	2026-09-18 16:08:12	2026-09-18 16:08:12	1
2089	6	4	M	3	3	0	0	2	1	0	0	0	3	0	2026-09-18 16:08:12	2026-09-18 16:08:12	3
2090	6	4	F	2	2	0	0	1	1	0	0	0	2	0	2026-09-18 16:08:12	2026-09-18 16:08:12	2
2091	6	5	M	0	0	0	0	0	0	0	0	0	0	0	2026-09-18 16:08:12	2026-09-18 16:08:12	0
2092	6	5	F	0	0	0	0	0	0	0	0	0	0	0	2026-09-18 16:08:12	2026-09-18 16:08:12	0
2093	6	6	M	0	0	0	0	0	0	0	0	0	0	0	2026-09-18 16:08:12	2026-09-18 16:08:12	0
2094	6	6	F	0	0	0	0	0	0	0	0	0	0	0	2026-09-18 16:08:12	2026-09-18 16:08:12	0
2095	6	7	M	0	0	0	0	0	0	0	0	0	0	0	2026-09-18 16:08:12	2026-09-18 16:08:12	0
2096	6	7	F	0	0	0	0	0	0	0	0	0	0	0	2026-09-18 16:08:12	2026-09-18 16:08:12	0
\.


--
-- Data for Name: report_periods; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.report_periods (id, school_year_id, name, month, created_at, updated_at, measurement_period) FROM stdin;
4	6	Baseline	9	2026-09-13 22:45:51	2026-09-13 22:45:51	baseline
5	6	Midline	9	2026-09-14 00:25:50	2026-09-14 00:25:50	mid
6	6	Endline	9	2026-09-14 00:26:53	2026-09-14 00:26:53	end
\.


--
-- Data for Name: sbfp_parent_approval_requests; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sbfp_parent_approval_requests (id, sbfp_participant_id, email, token_hash, weight, height, bmi, bmi_category, status, expires_at, sent_at, responded_at, decision_reason, closed_reason, closed_by_user_id, created_at, updated_at) FROM stdin;
1	112	nutrisight1@mailto.plus	eb28ab840956c394ba3018be75373725fc2b634b11869fd6305109fe638ee0e8	40.00	165.00	14.69	Severely Wasted	approved	2026-09-23 17:35:41	2026-09-20 17:35:41	2026-09-20 17:36:39	\N	\N	\N	2026-09-20 17:35:41	2026-09-20 17:36:39
2	113	nutrisight1@mailto.plus	9086e6cec37e26979adf6ba0d150461b325f0af5e3e47ec09cb1b4f87b9e969d	46.00	165.00	16.90	Wasted	approved	2026-09-23 19:55:43	2026-09-20 19:55:43	2026-09-20 19:58:07	\N	\N	\N	2026-09-20 19:55:43	2026-09-20 19:58:07
3	114	Maria@example.com	20293ea64b6caf0c41a796ed09d65b2a021fb82f2a6313b38cbca4611b1ff409	46.00	165.00	16.90	Wasted	superseded	2026-09-23 20:05:16	2026-09-20 20:05:16	\N	\N	manual_staff_decision	33	2026-09-20 20:05:16	2026-09-20 20:05:57
4	115	moltenterabyte@gmail.com	1234e4afe49036446ee86ad2ffacebc59fa9af51db00b7df064ab3b9be9a6280	46.00	165.00	16.90	Wasted	approved	2026-09-23 20:17:10	2026-09-20 20:17:10	2026-09-20 20:18:10	\N	\N	\N	2026-09-20 20:17:11	2026-09-20 20:18:10
5	116	santos.richarddavid@gmail.com	16652723add1909a58009a8ac239ff27242bf87063396d9750a22971869fe9b5	46.00	165.00	16.90	Wasted	approved	2026-09-23 20:27:24	2026-09-20 20:27:24	2026-09-20 20:29:21	\N	\N	\N	2026-09-20 20:27:24	2026-09-20 20:29:21
6	117	delacruz@gmail.com	d12519c6e457612c70f84752fe4391ad62df2250733767c6b8b40b2fcebbfa62	21.00	231.00	3.94	Severely Wasted	superseded	2026-09-25 19:39:53	2026-09-22 19:39:53	\N	\N	manual_staff_decision	33	2026-09-22 19:39:53	2026-09-22 19:44:20
7	60	moltenterabyte@gmail.com	4a292b097d7614038eed234dd59581cac109fdb428431ed6c5c56f766335be28	46.00	165.00	16.90	Wasted	approved	2026-09-25 23:31:44	2026-09-22 23:31:44	2026-09-22 23:33:17	\N	\N	\N	2026-09-22 23:31:44	2026-09-22 23:33:17
8	118	santos@gmail.com	3e17804d1a5e2447240ae0d1030990b01478fedca865f9fb490397dd794ea19d	50.00	165.00	18.37	Wasted	superseded	2026-09-26 13:52:25	2026-09-23 13:52:25	\N	\N	guardian_email_changed	33	2026-09-23 13:52:25	2026-09-23 13:53:21
9	118	santos.richarddavid@gmail.com	fe1304931e9199f90f507eac44bf590683e8992ffbb5a4053e48bd5c6586e94e	50.00	165.00	18.37	Wasted	approved	2026-09-26 13:53:21	2026-09-23 13:53:21	2026-09-23 13:55:09	\N	\N	\N	2026-09-23 13:53:21	2026-09-23 13:55:09
10	119	moltenterabyte@gmail.com	d1e95fcce61b395dd9f439c93ba142010e150efd4014caf88c4d9fc66be47eb5	46.00	165.00	16.90	Wasted	superseded	2026-09-28 09:39:17	2026-09-25 09:39:17	\N	\N	manual_staff_decision	26	2026-09-25 09:39:17	2026-09-25 09:53:45
\.


--
-- Data for Name: sbfp_participants; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sbfp_participants (id, enrollment_id, parent_consent, disapproval_reason, created_at, updated_at, profile_image_url) FROM stdin;
62	62	\N	\N	2026-09-13 23:01:31	2026-09-13 23:01:31	\N
63	63	\N	\N	2026-09-13 23:07:19	2026-09-13 23:07:19	\N
64	64	\N	\N	2026-09-13 23:08:53	2026-09-13 23:08:53	\N
65	65	\N	\N	2026-09-13 23:10:09	2026-09-13 23:10:09	\N
66	66	\N	\N	2026-09-13 23:11:28	2026-09-13 23:11:28	\N
98	98	approved	\N	2026-09-16 13:29:36	2026-09-20 16:39:50	https://pub-c7d0618ef09947b6a5311ff2f73ab695.r2.dev/sbfp-profiles/ZP5mPh30iJEp0DeKQHe4lZgc4PqVTjOoWj17NExu.webp
58	58	disapproved	unwilling	2026-09-13 22:50:53	2026-09-13 23:12:52	\N
107	107	\N	\N	2026-09-20 16:42:03	2026-09-20 16:42:03	\N
72	72	\N	\N	2026-09-13 23:29:11	2026-09-13 23:29:11	\N
73	73	\N	\N	2026-09-13 23:30:40	2026-09-13 23:30:40	\N
74	74	\N	\N	2026-09-13 23:31:44	2026-09-13 23:31:44	\N
75	75	\N	\N	2026-09-13 23:33:45	2026-09-13 23:33:45	\N
76	76	\N	\N	2026-09-13 23:35:15	2026-09-13 23:35:15	\N
70	70	disapproved	unwilling	2026-09-13 23:25:52	2026-09-13 23:36:09	\N
108	108	\N	\N	2026-09-20 16:43:18	2026-09-20 16:43:18	\N
109	109	\N	\N	2026-09-20 16:44:20	2026-09-20 16:44:20	\N
60	60	approved	\N	2026-09-13 22:58:32	2026-09-22 23:33:17	https://pub-c7d0618ef09947b6a5311ff2f73ab695.r2.dev/sbfp-profiles/FRXKIJGoiVcoMGbQ2yluLsmWMsqkk2zhoZe9Jace.jpg
101	101	approved	\N	2026-09-18 15:59:54	2026-09-20 16:45:07	\N
82	82	\N	\N	2026-09-13 23:45:50	2026-09-13 23:45:50	\N
83	83	\N	\N	2026-09-13 23:50:23	2026-09-13 23:50:23	\N
84	84	\N	\N	2026-09-13 23:51:38	2026-09-13 23:51:38	\N
85	85	\N	\N	2026-09-13 23:52:47	2026-09-13 23:52:47	\N
86	86	\N	\N	2026-09-13 23:53:51	2026-09-13 23:53:51	\N
77	77	approved	\N	2026-09-13 23:40:05	2026-09-13 23:57:36	\N
78	78	approved	\N	2026-09-13 23:41:06	2026-09-13 23:57:37	\N
79	79	disapproved	unwilling	2026-09-13 23:42:01	2026-09-13 23:57:37	\N
80	80	disapproved	unwilling	2026-09-13 23:43:13	2026-09-13 23:57:37	\N
81	81	disapproved	Milk Protein Allergy	2026-09-13 23:44:17	2026-09-13 23:57:38	\N
92	92	\N	\N	2026-09-14 00:06:29	2026-09-14 00:06:29	\N
93	93	\N	\N	2026-09-14 00:07:54	2026-09-14 00:07:54	\N
94	94	\N	\N	2026-09-14 00:09:04	2026-09-14 00:09:04	\N
95	95	\N	\N	2026-09-14 00:10:37	2026-09-14 00:10:37	\N
96	96	\N	\N	2026-09-14 00:11:55	2026-09-14 00:11:55	\N
110	110	\N	\N	2026-09-20 16:45:37	2026-09-20 16:45:37	\N
88	88	approved	\N	2026-09-14 00:02:14	2026-09-14 00:12:21	\N
89	89	approved	\N	2026-09-14 00:03:29	2026-09-14 00:12:22	\N
90	90	approved	\N	2026-09-14 00:04:29	2026-09-14 00:12:22	\N
91	91	approved	\N	2026-09-14 00:05:25	2026-09-14 00:12:22	\N
68	68	approved	\N	2026-09-13 23:22:44	2026-09-14 17:06:32	https://pub-c7d0618ef09947b6a5311ff2f73ab695.r2.dev/sbfp-profiles/VjLfTpvb0AOrBf6hLIu0OX1p0YJbzrMsYizcNw9q.jpg
69	69	approved	\N	2026-09-13 23:24:46	2026-09-14 17:21:54	https://pub-c7d0618ef09947b6a5311ff2f73ab695.r2.dev/sbfp-profiles/dzaDdudo3LawjuXOzfTfkJSsmCqu4aMaVMkMjMix.jpg
67	67	approved	\N	2026-09-13 23:21:28	2026-09-14 17:22:15	https://pub-c7d0618ef09947b6a5311ff2f73ab695.r2.dev/sbfp-profiles/BgGAL5UnNlyuTudOGYR0hY0SpmjN5aCXKrq3mSAA.jpg
71	71	approved	\N	2026-09-13 23:27:44	2026-09-14 17:25:54	https://pub-c7d0618ef09947b6a5311ff2f73ab695.r2.dev/sbfp-profiles/5uTdfRTwVI753SFkG0R8JrgKsmfbonuuVxKuaUdI.jpg
57	57	approved	\N	2026-09-13 22:45:51	2026-09-14 17:52:39	https://pub-c7d0618ef09947b6a5311ff2f73ab695.r2.dev/sbfp-profiles/oS56pwHZO8jAZB2MEwiX6JjcHdJQSzPXaRiLI0PQ.jpg
87	87	approved	\N	2026-09-14 00:01:16	2026-09-14 18:15:24	https://pub-c7d0618ef09947b6a5311ff2f73ab695.r2.dev/sbfp-profiles/qa7Gkh6wIUQP4mdZSYTvDBjlpZPvWVakHfymPisr.webp
111	111	\N	\N	2026-09-20 16:47:32	2026-09-20 16:47:32	\N
103	103	approved	\N	2026-09-20 16:36:18	2026-09-20 16:54:39	\N
61	61	approved	\N	2026-09-13 23:00:12	2026-09-16 11:32:43	https://pub-c7d0618ef09947b6a5311ff2f73ab695.r2.dev/sbfp-profiles/HbUTJJxqFq60fdSb1CuwPw2le4cyqoeqBBDsFuSR.jpg
118	118	approved	\N	2026-09-23 13:52:09	2026-09-23 13:55:09	\N
59	59	disapproved	incomplete	2026-09-13 22:53:00	2026-09-18 10:48:37	\N
100	100	approved	\N	2026-09-18 13:44:08	2026-09-18 13:44:33	\N
119	119	approved	\N	2026-09-25 09:39:17	2026-09-25 09:53:45	\N
102	102	approved	\N	2026-09-20 16:34:06	2026-09-20 16:54:39	\N
105	105	approved	\N	2026-09-20 16:39:52	2026-09-20 16:54:39	\N
106	106	approved	\N	2026-09-20 16:40:58	2026-09-20 16:54:39	\N
104	104	approved	\N	2026-09-20 16:38:34	2026-09-20 16:54:39	\N
112	112	approved	\N	2026-09-20 17:35:41	2026-09-20 17:59:27	\N
97	97	approved	\N	2026-09-16 11:16:03	2026-09-20 17:59:53	https://pub-c7d0618ef09947b6a5311ff2f73ab695.r2.dev/sbfp-profiles/9HLeEb92izs2eN1Dbp15CwqpcR3ukJ9IDqOz9MLG.webp
99	99	approved	\N	2026-09-18 10:46:29	2026-09-20 18:02:38	https://pub-c7d0618ef09947b6a5311ff2f73ab695.r2.dev/sbfp-profiles/UiKwXOOjBMENDtxGVUtNLjuNAKDf3lykOJ4SRVnH.png
113	113	approved	\N	2026-09-20 19:55:43	2026-09-20 19:58:06	\N
114	114	approved	\N	2026-09-20 20:05:16	2026-09-20 20:05:57	\N
115	115	approved	\N	2026-09-20 20:17:10	2026-09-20 20:27:14	\N
116	116	approved	\N	2026-09-20 20:27:24	2026-09-20 22:49:42	https://pub-c7d0618ef09947b6a5311ff2f73ab695.r2.dev/sbfp-profiles/B3oErGYh9BLwSQTrlUhNMPNjXHkCIuOQdLJh189A.jpg
117	117	approved	\N	2026-09-22 19:39:53	2026-09-22 19:46:41	https://pub-c7d0618ef09947b6a5311ff2f73ab695.r2.dev/sbfp-profiles/Jx3tunUqDrgT0a10P7r9ci99iq5caYxsX8giKaqi.jpg
\.


--
-- Data for Name: school_year_user_records; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.school_year_user_records (id, school_year_id, user_id, role, deped_id, "position", advisory_grade_level, advisory_section, created_at, updated_at) FROM stdin;
9	6	28	super_admin	100003	Master Teacher II			2026-09-13 22:00:10	2026-09-13 22:00:10
11	6	32	encoder	100005	Teacher II	3	A	2026-09-13 22:00:10	2026-09-13 22:00:10
14	7	27	admin	100002	Master Teacher I			2026-09-13 22:01:54	2026-09-13 22:01:54
15	7	28	super_admin	100003	Master Teacher II			2026-09-13 22:01:55	2026-09-13 22:01:55
18	7	33	encoder	100006	Teacher III	1	A	2026-09-13 22:01:55	2026-09-14 01:01:31
13	7	26	encoder	100001	Teacher I	4	B	2026-09-13 22:01:54	2026-09-14 01:03:10
17	7	32	encoder	100005	Teacher II	2	B	2026-09-13 22:01:55	2026-09-14 01:03:25
16	7	31	encoder	100004	Teacher I	3	A	2026-09-13 22:01:55	2026-09-14 01:03:48
7	6	26	encoder	100001	Teacher I	1	A	2026-09-13 22:00:09	2026-09-16 11:02:35
19	8	26	encoder	100001	Teacher I	4	B	2026-09-18 11:02:11	2026-09-18 11:02:11
20	8	27	admin	100002	Master Teacher I			2026-09-18 11:02:11	2026-09-18 11:02:11
21	8	28	super_admin	100003	Master Teacher II			2026-09-18 11:02:11	2026-09-18 11:02:11
22	8	31	encoder	100004	Teacher I	3	A	2026-09-18 11:02:11	2026-09-18 11:02:11
23	8	32	encoder	100005	Teacher II	2	B	2026-09-18 11:02:11	2026-09-18 11:02:11
24	8	33	encoder	100006	Teacher III	1	A	2026-09-18 11:02:11	2026-09-18 11:02:11
10	6	31	encoder	100010	Teacher I	2	B	2026-09-13 22:00:10	2026-09-20 19:39:40
8	6	27	admin	100002	Master Teacher I	\N	\N	2026-09-13 22:00:09	2026-09-20 22:06:04
12	6	33	encoder	100006	Teacher III	4	B	2026-09-13 22:00:10	2026-09-22 19:04:15
\.


--
-- Data for Name: school_years; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.school_years (id, year, is_active, start_date, end_date, created_at, updated_at) FROM stdin;
7	2027-2028	f	2027-06-07	2028-03-31	2026-09-13 22:01:54	2026-09-18 14:02:48
8	2028-2029	f	2026-09-18	2027-10-28	2026-09-18 11:02:11	2026-09-18 14:02:48
6	2026-2027	t	2026-06-01	2027-03-26	2026-09-13 22:00:09	2026-09-18 14:02:48
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
FIa19Lyu7zhCUjbnE5bAHZGf2SX81wvcznRH0VwT	\N	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36	eyJfdG9rZW4iOiJmSUU1SjlYQXRtZXVhTTZRd3laS0VSOXdya29FV3JzNVVCWmlRMWNyIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790295811
yRxoobzENOYSt8lyg4u1W4lycid71g07YmmhKiPO	\N	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36	eyJfdG9rZW4iOiJKTWZCbk1NNWUya0Vodm5aUnBMaEJDUU5KNG5KNWc3cXBJMk9mWDJoIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790295812
AZ1FYnIcVPeCp5Z6qmWjOk8rRkW0m9VExOS3zvIm	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJFZzlZNGFtNWthZ3NHMThKdE93QmZscXhCMjBNVnVrUHlSZ3ByaWcyIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790302503
XO0qRKXqK4yckuOSkzOKWlns47fJvfL5kkkI4FoD	\N	127.0.0.1	Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1	eyJfdG9rZW4iOiJpRG9KYjI0ZnA0VGx4RDJNNkQxd2l5QmhzQTlrZDJuTU9ydUFlY2ExIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790298838
wCH307AnP9g5jxDpF8qLh8BNKtllOU2tmwAfUlOg	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJ3OGlsN0c1TzFEYkhYblkzRlJCdHBTRnJZM0d6QTNlODhCNmFBdldBIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790298875
f8XFZe5Bm1t9DZjNYlyMoSKmY8rYEqBnFJPK5raY	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJERGFSOVFjcnk1YXhDR3c0ekV4S2Jqa1h3dXJPbXA4TVBpREdPY1owIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790316293
TI9c2ESLdUMbY8RUi3361AKha25OsWXLHCbZoac1	\N	127.0.0.1	Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1	eyJfdG9rZW4iOiJPcWJQTkdhQ3JjTU5xZjhJQnhaekhONWhOanI3RzU1dDhwOVN4eEZ2IiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790305647
udUiDgBMgvplNqGJjoqcghAAHSGx0xkOr3tb6Rsg	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJuTXgwcWJlc21hRWxTQkdVMjNJTVk3U05wbzhkeFE1UlJISjVoOGFlIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790309757
bguW8mUCxw6qgwuZ68G83m6se5lQNxH3QdI8II0u	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJ3VEdjMDE0R2JydXltTUozaEhzWEN3M3puUmZBQ0NkRmptVlozUU82IiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790319918
oEeoWTGXt79m9XavHSVr6N83uuLfYpgezut6g20D	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJlUjJrZ2RhYUF1U2RHSVZCdXIxSW9PWDNWbXQwSTkwQkdGbzlpczhmIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790323542
jaxnGvKbbVQTkkOzwNhxmS7limLw8QipgmMGG1QY	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJsNEJ5bjhOOXNRbm1BcHI1NE8wVzZBU043ZnBJNDVFaW1iRkpRQnY4IiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790326443
zRc2vhPVeH41w1vzV9riZzpVxBP3x4RYVrfyIBpk	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJ0cnlOYlZveHpIYzBpZVhEaUg5a0p1MXlHYVdoMUxQMW1oNlh0MVBsIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790333698
HLAqOXuoZ8x1ziEKO7wzo2UkfgAQ5tSXOO0HmwAB	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJMbks3SlpuTHJsb1E5Vzc4WFJOaFpjcndsM3B1WHg1MFpNYXJ2ZDFKIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790337326
aaeJVvsgsS0Mv9aLi3htp2mRUx0cFcJg3vluaEUv	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJrOHRLZHVSQTBVN3ZyQXV6VE1mRzc4aDRLMnFMemNVRVlncGNCd0ZmIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790301777
VhfnySEPUxQvNsAClKKI3t8zH4XQAhlSQJdkfXJP	\N	127.0.0.1	Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1	eyJfdG9rZW4iOiJuMTNITGxaRzZBV0tsakFDTmh4OFVqcVV4Z1NCd3E2WUJQeGt3dFc2IiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790301899
xulRxB8AhtuSp9FJ0gyiQRrXCuuho0KTy0YF1NSH	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJJWHIyTEhOZUV0YUp3MVBFRkdONlZ0R3d6Zk50ckIyZ2VIQzVrd0tIIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790303228
YZaEMPryHnlMMQnm4vUGptLRqddfIyB7Pq719qn1	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJnNmJ6U1RTWFpXZGRXZHZuZlF4bUMwR0ZCc09qWmtwajRiRGt0S00zIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790306132
Gd118SIbM6dPqNhREG2OEwmDEcruRszlFaN4IaVy	\N	127.0.0.1	Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1	eyJfdG9rZW4iOiI4UGtQUktuZVRDS2VDWlJLNlB3UUROd05QbTFUcTJEOVZLdEFPV0E2IiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790309841
kTVzlmPVH6l8FoodQgZRO3IATUjrH42aoSosiK5e	\N	127.0.0.1	Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1	eyJfdG9rZW4iOiJBQ1c1T3JxdUlqQUhCdkdMOGU1M2JmSHBEbTkybW52eGJKbVd2Z0tYIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790313147
6O6ArsLfHjLXHDawaUgXItC4K32etHpHM9WwCm2y	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJpUlFrcERhRlk0T1pjdEZ4anA3V0draXd1cFdxbElpRUxPU0lVS2N6IiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790317017
MI7EknfB9CnViezOT7TBIcF3FMoyejCYjhRHjbal	\N	127.0.0.1	Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1	eyJfdG9rZW4iOiJaM0ZVa3pCT2t2aEVrblhHdEw3V2NYNzlmNUswTGlUUzNWSmVDMWRiIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790320538
ANM1FdlpRVZyt0NGwkFF6hpragXlIP1cgqHDlIjL	\N	127.0.0.1	Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1	eyJfdG9rZW4iOiJ2WXQ5Y1BocWlKNlpnVWZsbjYxQ1pSTXFHY2FSMFh5clFjU29CSWdUIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790320612
Q8oIGG9mxPJiAoCl93YsMuMkBwdD0sWhjOT4qxd7	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiIxclMzcXAzU1N1ak94dXg0SnlDbk5BNlRnUEdrUGhGVzFHVWNjVElWIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790295974
jn4pUsuN7NkOiYGLO2SI4yZlDGPeYFtrPxZZQefM	\N	127.0.0.1	Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1	eyJfdG9rZW4iOiJ4OFdFZHRUTEdjdzZYejZSVm40ejBmODFQY2JFOHJpRTRCOXBsZENvIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790299116
QofvqYl1DVMb9z5MaCAa7MKIVIfJPE9BzCR73CPX	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJUQXJHQjdqZzdObWRqVnNmRW41V2FkSUZjckdUdFFSUXY1czFBaE5SIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790320644
Rj5Rp3KUCRMJOacqfKrCmtQbWx7HaGGFONeTqVlw	\N	127.0.0.1	Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1	eyJfdG9rZW4iOiIxV1lScVFrZmd6V3l3bWliVXc1eGo0aHR3QnB3S1NFQU9sMDdPQjYwIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790323771
nHy5yTYVUN4dQcXlsCmUBW2iSSD7yowWm2WpGrjF	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJCZ05YWnU2UmhUSlk1ektqNmx4NVlKQ093Z0w1V255U0d1eE9tVzJGIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790327169
zCGpHgYG0bOkfzEr6wSjUcpdxu6pucQXunB5IvTi	\N	127.0.0.1	Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.8010.52 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)	eyJfdG9rZW4iOiJ1c2JOUnJzSWdYWGk3eXY2czVqbWs4Vk5QSWJoMjNlSXZtQ0EzZnA3IiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790327207
W6b2YCRtx8oTevP8jmCa2lKvbgbPBp7IL6TsgvMl	\N	127.0.0.1	Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1	eyJfdG9rZW4iOiJac1piaHJLVmIyY2VYZDlNR0tDVWQ5a1czSHh2ellsdncxU055Vko0IiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790330547
MZ1oAtzkt92KlyyUiaZDoinNB9g982B3Nhwcw22t	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJJdm91RWFhMTdxWjZIVmhweGJpbGxWcnQ4QlVqNkpqRjNIdHFZQWN6IiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790334425
tXo3fdUYJ9MLrlbgVGBw3jHvA2Cu5ZZ8RhcHqGyK	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJuekhMVndwd1Vibnh1UGZodldpNnQxSkl0SUh6a3JXUVlmN21JQzd0IiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790338052
MiikAzJUKpZHaOlJnGrE00uj0SPtYgnaOi5AtRN9	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJBZHcwdXpsSkk4VVRXcE96SlNGcFdTcUlRbzNzQ3E2clZMVjJKQ0oyIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790303956
PmofOAEjtidCkTsYKp2QFBbuwti3idgoN10UxET9	\N	127.0.0.1	Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1	eyJfdG9rZW4iOiJsZUxSSmZNNEViVGNRS3g1OXFPMHpqbmlGanNteXpUbnBpclRrMzBWIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790306759
tS4QteKcizZ7K603JP9V34If89Xunp4dtOCqLLi6	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJTWnBqWVJ2QXlEZW9GU0RHSWYwRDNFMEtwaUlsaTgzMU5zc1Y4N2dvIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790306857
nCKoEzB2dnB0a7PrvSbleNUc58RhP0i0blB6VhNK	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJ4ejVHYnd5MVFLcVoxWXlBdnJNWmpteEZYallKdjZOclZnVzN6QjhsIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790310482
8pW1ta9sducXw3ogtx02lRIs4ukgwv0b5hcdHsB9	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJVbUwwWktqdFhDTDViWFN0NTBtcndRRlJ0NTJBOGs4SlJmc0FzSW5sIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790313389
6hnVWiCySxtdyMN9TRDti9wuIWeDveTbsP6xqbgK	\N	127.0.0.1	Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1	eyJfdG9rZW4iOiIwMVBIS0NwU1JhZVl1T3d1eEdqVmY3bmN6UFV5emJpRFlCTmJqWWFYIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790317244
aaemr9VMCKtMgAtrolibetecpORYL9j3GzYxZhEv	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiIySVM4NFFua2RIejIzQW13ejBKMXk4elhhN3oxa3JSWVRqNTFjWWJxIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790321369
oECLbTwnEldEWZl53OqfMApgsL7zNViJ2OGAFQPZ	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJMWjM5YjE1UWJuN0ZnN0dUMEN4QXJqNGY2REZ1NGZZeThyb1lyWkFEIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790296699
1D7STN5MZhgrx3gIqI4HsY5kPnzzBXa84AsS33gy	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJwOGJEUXJsQkJFZjBHTDhQV3RxTzdsUFplTUhHa2lVOWthMlpianhOIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790299600
Uk4AzfJT892BqKEP4IEYrC0IFVarPr2wI5KdocIG	\N	127.0.0.1	Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1	eyJfdG9rZW4iOiJIRFpCRkVXTFNtUWRuOEU5cktlWURJMk9FMlpuVGpVTk1LWkM3cDh5IiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790323903
RRWfaGe5qeSeKmMeYCvj2HvdLEOBsde86Xpr2A0u	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJGcHBBS3VuMUZTWUdvajhIeHRyN2o0RFAxRWRvam40NlU3TXhkcWlSIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790327895
timjg2IoGaHroD52NhxDfMA1rdWzboZ9uYIqkIyT	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJzNkVyMDk5SlFRYXBySEd3Tm9NZnlZdHA5YUNraXZZV2l6ZlhLRXQ2IiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790330799
O2tOKa7fEoh60IuFULmtapzv4penE0FIyakVzvwM	\N	127.0.0.1	Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1	eyJfdG9rZW4iOiJMMHJEelc5b1BJcEk5ZzhtYTlBTUo1eTFXVWw1c3lGdjVMNmhiTEVlIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790330859
MQbekgwiura9BtsREmXXhf0qiCKIQtidTyTyHONg	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJpOTcyR3JuaXJ0eVBaRmpyWDE0b0tSbzkyWU84WUtpY3d3VUVwbTdWIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790335150
Gb9NsQNhF49SVc0ThBHHUMR7q1oZ2jaQIGlZwDaI	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJ3RDZWejdhZ3o3S2Z6WkFaUUo3ZmkwMkUySkRpYkJIQUhYTk8yakRBIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790338778
jHOGyMkoR1XYFriv4fZcM8IsQ4vgB2Lef81Xj5Qn	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJPVkZXZGlmYVlkVXhRM21XNW9JSXJxY1RiaFZ2cUFvRGE3eEM1TUl3IiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790304681
fp1IHzSavs852wx3w4vRjUup2CGkeY0JHKpK2Y6C	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJlWlZMQ3BacUZJQVZjVWdYc3F2YkRLQkdqQVZ1bHg4bDJHaEZ6SE1MIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790307582
F2vBNZ9GHwTrEuTw96nJPtgUoAskbuhYNYkumBJC	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJLUEtEZ2lOemZaYnlGOHZFd1VrWDdPUHZlRTRvSDNTdGhxUkJWUWI1IiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790311209
sJa17EkvO3BDDaOP6OPfZpZKjYPv936ceG9KCNDn	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJIa25WTmlsb2djYzU3NGNaVktNTU5PdktLYkw5T1RUQkhuNlB4bjZ1IiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790314114
u9rjS0gJ6ZEcRrEryL1PT4mlPwfpGJKnTWZ2TWYl	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJFTkZMZEZVNWk0Z3l3WUtBdGJNUmROYVQxTjRXcHpYcXlnbGZ0ME9HIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790317743
ezIotoRPng8POGqxq2zZSTVNpnOcLICmoEG08dr3	\N	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36	eyJfdG9rZW4iOiJUMUNJTDNJY2ZtTVB2RHJjOUgxa0dybVRHTm9QZ0xHN3RDcVBpOUhWIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790317771
iHMhtEYxLqLrRTcnyvCCuy84AZ1demVzkMRL2dWQ	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJJSGZmS3hGWkUyQ3pLQnJaZVhNZVZJOEFIM3hLWXFZSXNYSElyeGtuIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790322094
XTWTkDkehXy0zHUqXG13WB4TkCujGx01tHBdzdSD	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJkYWhvZmpJR3ZrUU1pOXNVVXZDaTZoamRIYjNMZ0JiOEtiTG9SVksxIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790324266
I2NYdKruV2DPvyC19vUKX2uulpub3jrLt6GXo1s5	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJTbWdrU3hjdDVieE9RdFVGUlhRblBxbGxZTWtFc3JaVU9RT0E0ZzB0IiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790328621
mjTkQZp19wL06zVqE9khY143uuap9L2GnWQzQ4uw	\N	127.0.0.1	Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1	eyJfdG9rZW4iOiJ4VHVhNkJrMmg5aGZuRDNTWE9ObnFFTTJITlBST2dCS2VGWGtkU1dCIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790331502
Dfnvl5LOAHcFYxX066FLkmTr6ZZgCMDBRYnOcLBP	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJaRHRubDFuaTMyUWMwakdQUmJHU1Q0MFVsclNxWkVRV09KV2VUQ1Q2IiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790331524
llm0G1LsNUCy7R64xwPheu6WcoyrOGCrnqCqU8wA	\N	127.0.0.1	Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1	eyJfdG9rZW4iOiI5eE5uWGpURXhKTzFzbUx1SVUzODZHb2xQdE5rS2tqUk9QTDhCNTh6IiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790335651
lYUdfpXWR4yrG3I7BRm1tBeccm1YGWriijbiYCbc	\N	127.0.0.1	Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1	eyJfdG9rZW4iOiJSRXJqVXYwVE9FR3hzQ2I3WE1hRWNOSmY0YUJLZXhsTVJKTUF2NEROIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790297334
1wRxgFjFUzn50AREU5CNx2O6MPWQscy8HyLRmUdc	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJSZXhUYlNTSHlUd3pqR0k2NGZObW1EY2xLaGVjb2xGT0RVNUF2WWQ1IiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790297424
jasgd1eGSUse6LYuDHbonBYmy7pH5IuFI3jAIAfZ	\N	127.0.0.1	Mozilla/5.0 (compatible; webatlabot/1.0; +https://webatla.com/bot; abuse@webatla.com)	eyJfdG9rZW4iOiJ0ek9LbHFMM1hCWmI4NVl3R0tzb3psaWdId1B4djdYdGVZZjZpMmlFIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790300125
NHYpFOVe8AKAPUsfyYGouD6Unwp5KE2Wx9JPxh4R	\N	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36	eyJfdG9rZW4iOiJFWk9teW1qVUNEWWN0anA0c2RnRU5VUTVMZVJaY1lFZ2RRVXczSWtkIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJhY3RpdmVfc2Nob29sX3llYXJfaWQiOjYsIl9wcmV2aW91cyI6eyJ1cmwiOiJodHRwOlwvXC9udXRyaXNpZ2h0LnRlY2giLCJyb3V0ZSI6ImhvbWUifX0=	1790305125
IGFe9vp3bx1hCBgi3sQWkkTXqDLektgk6Md5NyzJ	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJxTVdlMUdXaVFxNnV3cFRSWk84blJ4bnpOQzdwRlpMRzdxckFIbGRiIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790308306
T9fNNCzxexQCZ32v4zwgLyRykQlyuXTKtrClRQp6	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJUT2F4anREQTRNdzZXRFlDUkh3OTZ1TWZwdThodXZhSURPcDZLb0dHIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790311935
Glear7RMc8a8IMuIxnGoSWQrrx4gm41LCyiAUu4p	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJpZWZjYmpRRVhFaEZjOTBqR3RjbzlPY0FVbGtYdTluTFRCVjlsZGg3IiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790314840
KXLpVhsOV9cZr8vIrDh17aDOekSW6UIQlbNeKchL	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJwRTFqVWZLcHJhU1hXZ1pndUx5cm5oSm5qUjRJc3A2cWYzcTZiRmsyIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790318469
R70Ox1HZ9uew0sSYN4Ya87i1jGEv8U05HoV17n2y	\N	127.0.0.1	Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36	eyJfdG9rZW4iOiJoTzNWbElPTGVhbmx2UllNaDlwbkh0S2NjYkRQYTJxTXRqNTZuVzR4IiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790322501
6UaQjl5brvuS88xLMs8AmqudOKDSvYs0dsivet55	\N	127.0.0.1	Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)	eyJfdG9rZW4iOiJpd1Rrb01oY1dpYlZUc29Uanp5SDIyclFpSVBVU0FhclZOVUVVb2tWIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790298115
lI0DRUnvMBLpb47IpOS5nIb1yORQvhLTIGrUF8ha	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJjODlaU0tjaWdGZHg2MHVBUWxEa01FU3pQTUxrNms4SkpEUXRIQnRTIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790324992
0fd4sMwyUgwqTio2WYLIqMkEZEcXv37nE9M8OI26	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJkc0Zsa25jaHpSQm1BZ3BTYWZGaVBYT0U1MXhCbUppYzVhWkxydGp5IiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790329347
8PHj7Qi7esjmLL1F6jLV01lyt2zjDCDoiF4CCu6B	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJBMHJzWXNrN1g0OWQ2OUJDRVJIN3BHWTdOQkVnV2VwUTJ4cTM3eTRiIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790332250
GgrMAJXzf8jgeGL4trZomlZVKf2J2oT4D4YWeSmh	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJCcHVObzdJaXFFQm9NdDlKZ3dXc3ZFeWpqOEFJR01rd3JkZmJ5Y0JiIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790335876
Zp1VzH23ma7bddVhHSgLXo0WKdmbZC0AOxmAMREa	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJEWmxSRkROY09SRUpWWkVMSWlRWTVXeEp6ZHJmMXBtTjllQlBndk92IiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790298150
mvpgUzqbZ8yjQIaz6QGhzfXCLnbwjaLTzvNfrwwR	\N	127.0.0.1	Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36	eyJfdG9rZW4iOiI3MjM1amFvM2piQXZWOGNYN2FxMUVYdlRzZ1RWU1dyU3lYdUM1N1F0IiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790335916
O5FEuvkk5ryrkmtfNxJaUcMS17a2gruUdG0jdYMG	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJ3Vk9CTGlWdVhJN0pEdDBMRno2WG9sRzNDQVUwdDhTTEpoZldQTlIzIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790300325
bwOpGd4BuLHmw6Z66h3NxoyYEvVFkWNNLi7Mhc88	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJUakdDWU9UNkM0ZVlDTGNyVzdDNDJQdjA4TFN5dkMyUEdtT1puNUJCIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790305407
YKE2gGUyFXZ8uXJLKfGvJj965GPujWDNtBRWazuv	\N	127.0.0.1	Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1	eyJfdG9rZW4iOiJzakJ6SjhEQ3pTdlA3M1dKTlRvSzFOWDEzY3RRU3BBa0sySEZCaVRnIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790305452
KQJDtkuCZZvThaFYlHOPTq3PJd8uIULA4WV4IXms	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJOVFVqYmUydDRFNDZQZm85R0J2Z3p2czFseGV6d1N4Z1hzR1ZONmNZIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790309031
4ISzi5IWY2LgmD3LgVATNxdWU1QcTxxDdQwbRKaB	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJSUXVPTWdnbkRaNTBmbGp1cjBxbm1OWXB0TlhrOEc4WXJxbjdvOXVlIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790312661
2yvfnJgwB92vGtoXvhavsKeYnvT9agjOHGfEFNP2	\N	127.0.0.1	Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1	eyJfdG9rZW4iOiJsVUxOSGhqYjRPdk1VSXA3UU9PV0tYUHpETEE1SE1QRzRDSlVjSlgwIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790312756
t49zT0lvNPrjJBkyiHqymzy8RgY23jywD20YIARe	\N	127.0.0.1	Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36	eyJfdG9rZW4iOiJlTk1KcXltNVZIYXNJM2l4ZTRwbmVVY2F5S044U3hRTjhnZ2lyeUt1IiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790315450
wllFEMwRiGqD8T3rN50SDF9aro9HHRzZ1EBpYVym	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJlWGFONHZWQ2g5ZXdhRE9UT043VmNPMnBCOGtGTjFVc1pSNTRSRzVJIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790315567
ry5xNbbQtwmDiRLGj82r00lbrj6rolhsCZpvapRw	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiI3YnJtVDI0b3FZNTJpbDJZeks3SW5VSTEwb1JYdXRIdEhESUNTbEdUIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790319194
B12IeMPxLbqSUWpU7rXDl60UUpQPhlGyEjxHrxyo	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJ3bVlGWHlkckF4ZTJtcW42ek9vNXVxWklPaVJLRmdQYTdZUGVKcVZMIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790322818
NOJnhnUcHpFa0U5TSvF9WNoL37BpvtEupHvhRqsL	\N	127.0.0.1	Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1	eyJfdG9rZW4iOiJLb2ZQd1Fuald6YjE3Y3dVcHBiMHFBRmhnSERpamhJdG56TjZBWUY2IiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbnV0cmlzaWdodC50ZWNoIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=	1790298519
ogTYsOLBcHknkMAZYem8DpbJCdcOyGvS82tTvsQT	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJzSTY5TzFNV01KemlveTFVRmlTQmIxUTB6c0RGU3M1MGx3amRJcFdNIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790301051
ua1Lkud3RpsGhHiHgF8gOUCUZX1sURoI4wtO4v3v	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJBdFNxT0QwMGIwWlNvUm1tOGxNbGp5OW5neWo3SktYQW80Nkh2Q1JFIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790325717
e6V0CYFUH9jvCN2AinAjPy3az2V5cNwou50OWFAd	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJOdnFuanVlU2czWmRaa09HMU40bmg2dUNMeXk5NE0xc3drYkJ6M1RJIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790330072
kwK1PurzNaIrk5RsUzoyJ7fCvONHdnWTF4lX30jL	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJGMEVNczBLenlScTBuYVZHNGxlcGJ2eWhaNTdxWDd3Tk56akdiSFNKIiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790332974
2nFfX8in50e3izqOMYWTj6xsEDXRKxZLdaTzYmu2	\N	127.0.0.1	Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)	eyJfdG9rZW4iOiJnNHRFd2Y3UFhZaDdRdXpFUlBTb1NEVTRvN3pYdEZ1Wm9jRUg3cE85IiwiYWN0aXZlX3NjaG9vbF95ZWFyX2lkIjo2LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19	1790336601
\.


--
-- Data for Name: student_attendance_records; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.student_attendance_records (id, sbfp_participant_id, recorded_by_user_id, attendance_date, status, created_at, updated_at) FROM stdin;
56	59	26	2026-09-14	present	2026-09-14 18:39:53	2026-09-14 18:39:53
57	61	26	2026-09-14	present	2026-09-14 18:40:06	2026-09-14 18:40:06
58	67	31	2026-09-14	present	2026-09-14 18:43:26	2026-09-14 18:43:26
59	68	31	2026-09-14	present	2026-09-14 18:43:38	2026-09-14 18:43:38
60	69	31	2026-09-14	present	2026-09-14 18:43:49	2026-09-14 18:43:49
61	71	31	2026-09-14	present	2026-09-14 18:44:01	2026-09-14 18:44:01
62	77	32	2026-09-14	present	2026-09-14 18:47:41	2026-09-14 18:47:41
63	78	32	2026-09-14	present	2026-09-14 18:47:49	2026-09-14 18:47:49
64	87	33	2026-09-14	present	2026-09-14 18:51:04	2026-09-14 18:51:04
65	88	33	2026-09-14	present	2026-09-14 18:51:11	2026-09-14 18:51:11
66	89	33	2026-09-14	present	2026-09-14 18:51:23	2026-09-14 18:51:23
67	90	33	2026-09-14	absent	2026-09-14 18:51:29	2026-09-14 18:52:40
68	91	33	2026-09-14	absent	2026-09-14 18:51:37	2026-09-14 18:52:50
55	57	26	2026-09-14	absent	2026-09-14 18:39:38	2026-09-15 22:52:29
70	57	26	2026-09-16	absent	2026-09-16 10:37:51	2026-09-16 11:11:19
71	59	26	2026-09-16	absent	2026-09-16 10:38:36	2026-09-16 11:11:22
73	60	26	2026-09-16	present	2026-09-16 11:11:49	2026-09-16 11:11:59
72	61	26	2026-09-16	present	2026-09-16 10:38:50	2026-09-16 11:12:13
74	97	27	2026-09-16	present	2026-09-16 14:03:28	2026-09-16 14:03:28
75	60	26	2026-09-14	absent	2026-09-17 13:39:52	2026-09-17 13:39:52
76	97	26	2026-09-14	absent	2026-09-17 13:39:52	2026-09-17 13:39:52
78	67	26	2026-09-16	absent	2026-09-17 13:39:52	2026-09-17 13:39:52
79	68	26	2026-09-16	absent	2026-09-17 13:39:52	2026-09-17 13:39:52
80	69	26	2026-09-16	absent	2026-09-17 13:39:52	2026-09-17 13:39:52
81	71	26	2026-09-16	absent	2026-09-17 13:39:52	2026-09-17 13:39:52
82	77	26	2026-09-16	absent	2026-09-17 13:39:52	2026-09-17 13:39:52
83	78	26	2026-09-16	absent	2026-09-17 13:39:52	2026-09-17 13:39:52
84	87	26	2026-09-16	absent	2026-09-17 13:39:52	2026-09-17 13:39:52
85	88	26	2026-09-16	absent	2026-09-17 13:39:52	2026-09-17 13:39:52
86	89	26	2026-09-16	absent	2026-09-17 13:39:52	2026-09-17 13:39:52
87	90	26	2026-09-16	absent	2026-09-17 13:39:52	2026-09-17 13:39:52
88	91	26	2026-09-16	absent	2026-09-17 13:39:52	2026-09-17 13:39:52
90	67	26	2026-09-17	absent	2026-09-18 13:57:38	2026-09-18 13:57:38
91	68	26	2026-09-17	absent	2026-09-18 13:57:38	2026-09-18 13:57:38
92	69	26	2026-09-17	absent	2026-09-18 13:57:38	2026-09-18 13:57:38
93	71	26	2026-09-17	absent	2026-09-18 13:57:38	2026-09-18 13:57:38
94	77	26	2026-09-17	absent	2026-09-18 13:57:38	2026-09-18 13:57:38
96	87	26	2026-09-17	absent	2026-09-18 13:57:38	2026-09-18 13:57:38
97	88	26	2026-09-17	absent	2026-09-18 13:57:38	2026-09-18 13:57:38
98	89	26	2026-09-17	absent	2026-09-18 13:57:38	2026-09-18 13:57:38
104	97	26	2026-09-17	absent	2026-09-18 13:57:38	2026-09-18 13:57:38
99	90	27	2026-09-17	present	2026-09-18 13:57:38	2026-09-18 14:22:23
106	97	26	2026-09-18	absent	2026-09-17 15:10:59	2026-09-17 15:10:59
108	78	27	2026-09-17	absent	2026-09-18 15:24:23	2026-09-18 15:24:23
109	91	27	2026-09-17	absent	2026-09-18 15:24:23	2026-09-18 15:24:23
110	57	27	2026-09-17	absent	2026-09-18 15:24:23	2026-09-18 15:24:23
111	61	27	2026-09-17	absent	2026-09-18 15:24:23	2026-09-18 15:24:23
113	67	27	2026-09-18	absent	2026-09-22 15:26:29	2026-09-22 15:26:29
114	68	27	2026-09-18	absent	2026-09-22 15:26:29	2026-09-22 15:26:29
115	69	27	2026-09-18	absent	2026-09-22 15:26:29	2026-09-22 15:26:29
116	71	27	2026-09-18	absent	2026-09-22 15:26:29	2026-09-22 15:26:29
117	77	27	2026-09-18	absent	2026-09-22 15:26:29	2026-09-22 15:26:29
118	78	27	2026-09-18	absent	2026-09-22 15:26:29	2026-09-22 15:26:29
119	87	27	2026-09-18	absent	2026-09-22 15:26:29	2026-09-22 15:26:29
120	88	27	2026-09-18	absent	2026-09-22 15:26:29	2026-09-22 15:26:29
121	89	27	2026-09-18	absent	2026-09-22 15:26:29	2026-09-22 15:26:29
122	90	27	2026-09-18	absent	2026-09-22 15:26:29	2026-09-22 15:26:29
123	91	27	2026-09-18	absent	2026-09-22 15:26:29	2026-09-22 15:26:29
124	57	27	2026-09-18	absent	2026-09-22 15:26:29	2026-09-22 15:26:29
125	60	27	2026-09-18	absent	2026-09-22 15:26:29	2026-09-22 15:26:29
126	61	27	2026-09-18	absent	2026-09-22 15:26:29	2026-09-22 15:26:29
102	60	26	2026-09-17	present	2026-09-18 13:57:38	2026-09-17 15:34:38
129	98	27	2026-09-17	present	2026-09-17 15:36:12	2026-09-17 15:36:12
77	98	27	2026-09-14	present	2026-09-17 13:39:52	2026-09-17 16:11:19
89	98	27	2026-09-16	present	2026-09-17 13:39:52	2026-09-17 16:11:34
127	98	27	2026-09-18	present	2026-09-22 15:26:29	2026-09-17 16:11:50
130	99	26	2026-09-14	absent	2026-09-18 10:50:32	2026-09-18 10:50:32
131	99	26	2026-09-16	absent	2026-09-18 10:50:32	2026-09-18 10:50:32
132	99	26	2026-09-17	absent	2026-09-18 10:50:33	2026-09-18 10:50:33
133	99	27	2026-09-18	present	2026-09-18 10:54:46	2026-09-18 10:54:46
134	100	27	2026-09-14	absent	2026-09-18 13:48:34	2026-09-18 13:48:34
135	100	27	2026-09-16	absent	2026-09-18 13:48:34	2026-09-18 13:48:34
136	100	27	2026-09-17	absent	2026-09-18 13:48:34	2026-09-18 13:48:34
137	100	27	2026-09-18	present	2026-09-18 13:50:12	2026-09-18 13:50:12
138	101	26	2026-09-14	absent	2026-09-18 16:02:14	2026-09-18 16:02:14
139	101	26	2026-09-16	absent	2026-09-18 16:02:14	2026-09-18 16:02:14
140	101	26	2026-09-17	absent	2026-09-18 16:02:14	2026-09-18 16:02:14
141	101	27	2026-09-18	present	2026-09-18 16:05:53	2026-09-18 16:05:53
142	67	26	2026-09-19	absent	2026-09-20 15:00:48	2026-09-20 15:00:48
143	68	26	2026-09-19	absent	2026-09-20 15:00:48	2026-09-20 15:00:48
144	69	26	2026-09-19	absent	2026-09-20 15:00:48	2026-09-20 15:00:48
145	71	26	2026-09-19	absent	2026-09-20 15:00:48	2026-09-20 15:00:48
146	77	26	2026-09-19	absent	2026-09-20 15:00:48	2026-09-20 15:00:48
147	78	26	2026-09-19	absent	2026-09-20 15:00:48	2026-09-20 15:00:48
148	87	26	2026-09-19	absent	2026-09-20 15:00:48	2026-09-20 15:00:48
149	88	26	2026-09-19	absent	2026-09-20 15:00:48	2026-09-20 15:00:48
150	89	26	2026-09-19	absent	2026-09-20 15:00:48	2026-09-20 15:00:48
151	90	26	2026-09-19	absent	2026-09-20 15:00:48	2026-09-20 15:00:48
152	91	26	2026-09-19	absent	2026-09-20 15:00:48	2026-09-20 15:00:48
153	57	26	2026-09-19	absent	2026-09-20 15:00:48	2026-09-20 15:00:48
154	60	26	2026-09-19	absent	2026-09-20 15:00:48	2026-09-20 15:00:48
155	61	26	2026-09-19	absent	2026-09-20 15:00:48	2026-09-20 15:00:48
156	97	26	2026-09-19	absent	2026-09-20 15:00:48	2026-09-20 15:00:48
157	98	26	2026-09-19	absent	2026-09-20 15:00:48	2026-09-20 15:00:48
158	99	26	2026-09-19	absent	2026-09-20 15:00:48	2026-09-20 15:00:48
159	100	26	2026-09-19	absent	2026-09-20 15:00:48	2026-09-20 15:00:48
160	101	26	2026-09-19	absent	2026-09-20 15:00:48	2026-09-20 15:00:48
161	57	27	2026-09-20	present	2026-09-20 16:26:13	2026-09-20 16:26:13
162	60	27	2026-09-20	present	2026-09-20 16:29:57	2026-09-20 16:29:57
163	102	26	2026-09-14	absent	2026-09-20 17:29:29	2026-09-20 17:29:29
164	103	26	2026-09-14	absent	2026-09-20 17:29:29	2026-09-20 17:29:29
165	104	26	2026-09-14	absent	2026-09-20 17:29:29	2026-09-20 17:29:29
166	105	26	2026-09-14	absent	2026-09-20 17:29:29	2026-09-20 17:29:29
167	106	26	2026-09-14	absent	2026-09-20 17:29:29	2026-09-20 17:29:29
168	102	26	2026-09-16	absent	2026-09-20 17:29:29	2026-09-20 17:29:29
169	103	26	2026-09-16	absent	2026-09-20 17:29:29	2026-09-20 17:29:29
170	104	26	2026-09-16	absent	2026-09-20 17:29:29	2026-09-20 17:29:29
171	105	26	2026-09-16	absent	2026-09-20 17:29:29	2026-09-20 17:29:29
172	106	26	2026-09-16	absent	2026-09-20 17:29:29	2026-09-20 17:29:29
173	102	26	2026-09-17	absent	2026-09-20 17:29:30	2026-09-20 17:29:30
174	103	26	2026-09-17	absent	2026-09-20 17:29:30	2026-09-20 17:29:30
175	104	26	2026-09-17	absent	2026-09-20 17:29:30	2026-09-20 17:29:30
176	105	26	2026-09-17	absent	2026-09-20 17:29:30	2026-09-20 17:29:30
177	106	26	2026-09-17	absent	2026-09-20 17:29:30	2026-09-20 17:29:30
178	102	26	2026-09-18	absent	2026-09-20 17:29:30	2026-09-20 17:29:30
179	103	26	2026-09-18	absent	2026-09-20 17:29:30	2026-09-20 17:29:30
180	104	26	2026-09-18	absent	2026-09-20 17:29:30	2026-09-20 17:29:30
181	105	26	2026-09-18	absent	2026-09-20 17:29:30	2026-09-20 17:29:30
182	106	26	2026-09-18	absent	2026-09-20 17:29:30	2026-09-20 17:29:30
183	102	26	2026-09-19	absent	2026-09-20 17:29:30	2026-09-20 17:29:30
184	103	26	2026-09-19	absent	2026-09-20 17:29:30	2026-09-20 17:29:30
185	104	26	2026-09-19	absent	2026-09-20 17:29:30	2026-09-20 17:29:30
186	105	26	2026-09-19	absent	2026-09-20 17:29:30	2026-09-20 17:29:30
187	106	26	2026-09-19	absent	2026-09-20 17:29:30	2026-09-20 17:29:30
188	112	27	2026-09-14	absent	2026-09-20 17:47:42	2026-09-20 17:47:42
189	112	27	2026-09-16	absent	2026-09-20 17:47:42	2026-09-20 17:47:42
190	112	27	2026-09-17	absent	2026-09-20 17:47:42	2026-09-20 17:47:42
191	112	27	2026-09-18	absent	2026-09-20 17:47:42	2026-09-20 17:47:42
192	112	27	2026-09-19	absent	2026-09-20 17:47:43	2026-09-20 17:47:43
193	113	27	2026-09-14	absent	2026-09-20 20:03:05	2026-09-20 20:03:05
194	113	27	2026-09-16	absent	2026-09-20 20:03:05	2026-09-20 20:03:05
195	113	27	2026-09-17	absent	2026-09-20 20:03:05	2026-09-20 20:03:05
196	113	27	2026-09-18	absent	2026-09-20 20:03:05	2026-09-20 20:03:05
197	113	27	2026-09-19	absent	2026-09-20 20:03:05	2026-09-20 20:03:05
198	114	27	2026-09-14	absent	2026-09-20 20:06:00	2026-09-20 20:06:00
199	114	27	2026-09-16	absent	2026-09-20 20:06:00	2026-09-20 20:06:00
200	114	27	2026-09-17	absent	2026-09-20 20:06:00	2026-09-20 20:06:00
201	114	27	2026-09-18	absent	2026-09-20 20:06:00	2026-09-20 20:06:00
202	114	27	2026-09-19	absent	2026-09-20 20:06:01	2026-09-20 20:06:01
203	115	26	2026-09-14	absent	2026-09-20 20:20:32	2026-09-20 20:20:32
204	115	26	2026-09-16	absent	2026-09-20 20:20:32	2026-09-20 20:20:32
205	115	26	2026-09-17	absent	2026-09-20 20:20:32	2026-09-20 20:20:32
206	115	26	2026-09-18	absent	2026-09-20 20:20:32	2026-09-20 20:20:32
207	115	26	2026-09-19	absent	2026-09-20 20:20:32	2026-09-20 20:20:32
209	116	27	2026-09-14	absent	2026-09-20 20:31:50	2026-09-20 20:31:50
210	116	27	2026-09-16	absent	2026-09-20 20:31:50	2026-09-20 20:31:50
211	116	27	2026-09-17	absent	2026-09-20 20:31:50	2026-09-20 20:31:50
212	116	27	2026-09-18	absent	2026-09-20 20:31:50	2026-09-20 20:31:50
213	116	27	2026-09-19	absent	2026-09-20 20:31:50	2026-09-20 20:31:50
214	90	33	2026-09-20	present	2026-09-20 22:50:18	2026-09-20 22:50:18
215	103	33	2026-09-20	present	2026-09-20 22:50:23	2026-09-20 22:50:23
216	104	33	2026-09-20	present	2026-09-20 22:50:26	2026-09-20 22:50:26
217	87	33	2026-09-20	present	2026-09-20 22:50:29	2026-09-20 22:50:29
218	106	33	2026-09-20	present	2026-09-20 22:50:32	2026-09-20 22:50:32
219	89	33	2026-09-20	present	2026-09-20 22:50:35	2026-09-20 22:50:35
220	105	33	2026-09-20	present	2026-09-20 22:50:38	2026-09-20 22:50:38
223	102	33	2026-09-20	present	2026-09-20 22:51:33	2026-09-20 22:51:33
224	88	33	2026-09-20	present	2026-09-20 22:53:49	2026-09-20 22:53:49
225	91	33	2026-09-20	present	2026-09-20 22:53:54	2026-09-20 22:53:54
226	98	27	2026-09-20	absent	2026-09-21 07:46:05	2026-09-21 07:46:05
227	101	27	2026-09-20	absent	2026-09-21 07:46:05	2026-09-21 07:46:05
228	77	27	2026-09-20	absent	2026-09-21 07:46:05	2026-09-21 07:46:05
229	78	27	2026-09-20	absent	2026-09-21 07:46:05	2026-09-21 07:46:05
230	68	27	2026-09-20	absent	2026-09-21 07:46:05	2026-09-21 07:46:05
231	69	27	2026-09-20	absent	2026-09-21 07:46:05	2026-09-21 07:46:05
232	67	27	2026-09-20	absent	2026-09-21 07:46:05	2026-09-21 07:46:05
233	71	27	2026-09-20	absent	2026-09-21 07:46:05	2026-09-21 07:46:05
234	61	27	2026-09-20	absent	2026-09-21 07:46:05	2026-09-21 07:46:05
235	100	27	2026-09-20	absent	2026-09-21 07:46:05	2026-09-21 07:46:05
236	112	27	2026-09-20	absent	2026-09-21 07:46:05	2026-09-21 07:46:05
237	97	27	2026-09-20	absent	2026-09-21 07:46:05	2026-09-21 07:46:05
238	99	27	2026-09-20	absent	2026-09-21 07:46:05	2026-09-21 07:46:05
239	113	27	2026-09-20	absent	2026-09-21 07:46:05	2026-09-21 07:46:05
240	114	27	2026-09-20	absent	2026-09-21 07:46:05	2026-09-21 07:46:05
241	115	27	2026-09-20	absent	2026-09-21 07:46:05	2026-09-21 07:46:05
242	116	27	2026-09-20	absent	2026-09-21 07:46:05	2026-09-21 07:46:05
243	117	33	2026-09-14	absent	2026-09-22 19:47:23	2026-09-22 19:47:23
244	117	33	2026-09-16	absent	2026-09-22 19:47:23	2026-09-22 19:47:23
245	117	33	2026-09-17	absent	2026-09-22 19:47:23	2026-09-22 19:47:23
246	117	33	2026-09-18	absent	2026-09-22 19:47:23	2026-09-22 19:47:23
247	117	33	2026-09-19	absent	2026-09-22 19:47:23	2026-09-22 19:47:23
248	117	33	2026-09-20	absent	2026-09-22 19:47:23	2026-09-22 19:47:23
250	119	27	2026-09-25	present	2026-09-25 10:02:20	2026-09-25 10:02:20
251	118	28	2026-09-14	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
252	119	28	2026-09-14	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
253	118	28	2026-09-16	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
254	119	28	2026-09-16	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
255	118	28	2026-09-17	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
256	119	28	2026-09-17	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
257	118	28	2026-09-18	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
258	119	28	2026-09-18	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
259	118	28	2026-09-19	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
260	119	28	2026-09-19	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
261	118	28	2026-09-20	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
262	119	28	2026-09-20	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
263	67	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
264	68	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
265	69	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
266	71	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
267	77	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
268	78	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
269	87	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
270	88	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
271	89	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
272	90	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
273	91	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
274	57	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
275	60	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
276	61	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
277	97	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
278	98	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
279	99	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
280	100	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
281	101	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
282	112	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
283	115	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
284	113	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
285	102	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
286	103	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
287	104	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
288	105	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
289	106	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
290	116	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
291	114	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
292	117	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
293	118	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
294	119	28	2026-09-22	absent	2026-09-25 10:54:38	2026-09-25 10:54:38
\.


--
-- Data for Name: student_feeding_records; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.student_feeding_records (id, sbfp_participant_id, recorded_by_user_id, feeding_date, meal_type, meal_served, photo, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: students; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.students (id, lrn, first_name, last_name, name_extension, middle_name, sex, birth_date, guardian_name, guardian_email, address, created_at, updated_at, guardian_contact) FROM stdin;
62	136542100006	Lea	Bautista	\N	S.	Female	2020-01-06	Ramon Bautista	r.bautista@example.com	11 Roxas St, Brgy Mabiga, Mabalacat City	2026-09-13 23:01:31	2026-09-13 23:01:31	09171117777
63	136542100007	Paul	Torres	\N	D.	Male	2020-01-07	Lita Torres	lita.t@example.com	22 Quezon St, Brgy Mabiga, Mabalacat City	2026-09-13 23:07:19	2026-09-13 23:07:19	09171118888
64	136542100008	Kim	Villanueva	\N	R.	Female	2020-01-08	Raul Villanueva	raul.v@example.com	33 Osmena St, Brgy Mabiga, Mabalacat City	2026-09-13 23:08:52	2026-09-13 23:08:52	09171119999
65	136542100009	Ian	Ramos	\N	L.	Male	2020-01-09	Nina Ramos	nina.r@example.com	44 Laurel St, Brgy Mabiga, Mabalacat City	2026-09-13 23:10:09	2026-09-13 23:10:09	09171110000
66	136542100010	Mia	Castro	\N	P.	Female	2026-01-10	Tomas Castro	tomas.c@example.com	55 Magsaysay St, Brgy Mabiga, Mabalacat City	2026-09-13 23:11:27	2026-09-13 23:11:27	09171111111
72	136542100016	Zoe	Rivera	\N	F.	Female	2020-01-16	Ruel Rivera	ruel.r@example.com	21 Marcos St, Brgy Mabiga, Mabalacat City	2026-09-13 23:29:11	2026-09-13 23:29:11	09172227777
73	136542100017	Leo	Ocampo	\N	G.	Male	2020-01-17	Lina Ocampo	lina.o@example.com	32 Garcia St, Brgy Mabiga, Mabalacat City	2026-09-13 23:30:40	2026-09-13 23:30:40	09172228888
74	136542100018	May	Aguilar	\N	H.	Female	2020-01-18	Rico Aguilar	rico.a@example.com	43 Quirino St, Brgy Mabiga, Mabalacat City	2026-09-13 23:31:44	2026-09-13 23:31:44	09172229999
75	136542100019	Ken	Suarez	\N	I.	Male	2020-01-19	Gina Suarez	gina.s@example.com	54 Roxas St, Brgy Mabiga, Mabalacat City	2026-09-13 23:33:45	2026-09-13 23:33:45	09172220000
76	136542100020	Joy	Ferrer	\N	J.	Female	2020-01-20	Lito Ferrer	lito.f@example.com	65 Osmena St, Brgy Mabiga, Mabalacat City	2026-09-13 23:35:14	2026-09-13 23:35:14	09172222222
82	136542100026	Ria	Sunga	\N	P.	Female	2020-01-26	Fred Sunga	fred.s@example.com	31 Estrada St, Brgy Mabiga, Mabalacat City	2026-09-13 23:45:50	2026-09-13 23:45:50	09173337777
83	136542100027	Ted	Manalo	\N	Q.	Male	2020-01-27	Gaya Manalo	gaya.m@example.com	42 Arroyo St, Brgy Mabiga, Mabalacat City	2026-09-13 23:50:23	2026-09-13 23:50:23	09173338888
84	136542100028	Amy	Valdes	\N	R.	Female	2020-01-28	Hugo Valdes	hugo.v@example.com	53 Duterte St, Brgy Mabiga, Mabalacat City	2026-09-13 23:51:38	2026-09-13 23:51:38	09173339999
85	136542100029	Dan	Lim	\N	S.	Male	2020-01-29	Ines Lim	ines.l@example.com	64 Marcos St, Brgy Mabiga, Mabalacat City	2026-09-13 23:52:47	2026-09-13 23:52:47	09173330000
86	136542100030	Fe	Tan	\N	T.	Female	2020-01-30	Jose Tan	jose.t@example.com	75 Garcia St, Brgy Mabiga, Mabalacat City	2026-09-13 23:53:51	2026-09-13 23:53:51	09173333333
92	136542100036	Sol	Yap	\N	Z.	Female	2020-02-05	Pio Yap	pio.y@example.com	41 Magsaysay St, Brgy Mabiga, Mabalacat City	2026-09-14 00:06:29	2026-09-14 00:06:29	09174447777
93	136542100037	Rey	Co	\N	A.	Male	2020-02-06	Quin Co	quin.c@example.com	52 Aquino St, Brgy Mabiga, Mabalacat City	2026-09-14 00:07:54	2026-09-14 00:07:54	09174448888
94	136542100038	Paz	Lee	\N	B.	Female	2020-02-07	Romy Lee	romy.l@example.com	63 Macapagal St, Brgy Mabiga, Mabalacat City	2026-09-14 00:09:04	2026-09-14 00:09:04	09174449999
95	136542100039	Tom	Ang	\N	C.	Male	2020-02-08	Sisa Ang	sisa.a@example.com	74 Estrada St, Brgy Mabiga, Mabalacat City	2026-09-14 00:10:36	2026-09-14 00:10:36	09174440000
96	136542100040	Mae	Dy	\N	D.	Female	2020-02-09	Tito Dy	tito.d@example.com	85 Arroyo St, Brgy Mabiga, Mabalacat City	2026-09-14 00:11:55	2026-09-14 00:11:55	09174444444
57	136542100001	Juan	Santos	Jr.	Perez	Male	2020-01-01	Maria Santos	nutrisight1@mailto.plus	12 Rizal St, Brgy Mabiga, Mabalacat City	2026-09-13 22:45:50	2026-09-14 18:31:12	09171112222
59	136542100003	Luis	Garcia	III	M.	Male	2020-01-03	Elena Garcia	nutrisight1@mailto.plus	56 Luna St, Brgy Mabiga, Mabalacat City	2026-09-13 22:53:00	2026-09-14 18:35:26	09171114444
61	136542100005	Mark	Cruz	\N	V.	Male	2020-01-05	Sofia Cruz	nutrisight1@mailto.plus	90 Aguinaldo St, Brgy Mabiga, Mabalacat City	2026-09-13 23:00:12	2026-09-14 18:35:43	09171116666
58	136542100002	Ana	Reyes	Cruz	\N	Female	2020-01-02	Jose Reyes	nutrisight1@mailto.plus	34 Mabini St, Brgy Mabiga, Mabalacat City	2026-09-13 22:50:53	2026-09-14 18:35:10	09171113333
67	136542100011	Carlos	Dizon	\N	A.	Male	2020-01-11	Clara Dizon	nutrisight1@mailto.plus	66 Aquino St, Brgy Mabiga, Mabalacat City	2026-09-13 23:21:28	2026-09-14 18:41:20	09172221111
68	136542100012	Rosa	Pineda	\N	B.	Female	2020-01-12	Juan Pineda	nutrisight1@mailto.plus	77 Macapagal St, Brgy Mabiga, Mabalacat City	2026-09-13 23:22:43	2026-09-14 18:41:42	09172223333
69	136542100013	Roy	Gutierrez	\N	C.	Male	2020-01-13	Alma Gutierrez	nutrisight1@mailto.plus	88 Estrada St, Brgy Mabiga, Mabalacat City	2026-09-13 23:24:45	2026-09-14 18:42:01	09172224444
70	136542100014	Eve	Navarro	\N	D.	Female	2020-01-14	Nilo Navarro	nutrisight1@mailto.plus	99 Arroyo St, Brgy Mabiga, Mabalacat City	2026-09-13 23:25:52	2026-09-14 18:42:17	09172225555
71	136542100015	Jay	Mercado	Jr.	E.	Male	2019-01-15	Sara Mercado	nutrisight1@mailto.plus	10 Duterte St, Brgy Mabiga, Mabalacat City	2026-09-13 23:27:44	2026-09-14 18:42:31	09172226666
77	136542100021	Sam	David	\N	K.	Male	2020-01-21	Nita David	nutrisight1@mailto.plus	76 Quezon St, Brgy Mabiga, Mabalacat City	2026-09-13 23:40:04	2026-09-14 18:45:56	09173331111
78	136542100022	Ivy	Lopez	\N	L.	Female	2020-01-22	Bert Lopez	nutrisight1@mailto.plus	87 Laurel St, Brgy Mabiga, Mabalacat City	2026-09-13 23:41:05	2026-09-14 18:46:11	09173332222
79	136542100023	Gil	Perez	\N	M.	Male	2020-01-23	Cora Perez	nutrisight1@mailto.plus	98 Magsaysay St, Brgy Mabiga, Mabalacat City	2026-09-13 23:42:01	2026-09-14 18:46:25	09173334444
80	136542100024	Pat	Gomez	\N	N.	Female	2020-01-24	Dino Gomez	nutrisight1@mailto.plus	19 Aquino St, Brgy Mabiga, Mabalacat City	2026-09-13 23:43:13	2026-09-14 18:46:38	09173335555
81	136542100025	Ray	Yabut	\N	O.	Male	2020-01-25	Elma Yabut	nutrisight1@mailto.plus	20 Macapagal St, Brgy Mabiga, Mabalacat City	2026-09-13 23:44:17	2026-09-14 18:46:54	09173336666
87	136542100031	Al	Go	\N	U.	Male	2020-01-31	Kim Go	nutrisight1@mailto.plus	86 Quirino St, Brgy Mabiga, Mabalacat City	2026-09-14 00:01:16	2026-09-14 18:48:50	09174441111
88	136542100032	Luz	Sy	\N	V.	Female	2020-02-01	Lino Sy	nutrisight1@mailto.plus	97 Roxas St, Brgy Mabiga, Mabalacat City	2026-09-14 00:02:14	2026-09-14 18:49:06	09174442222
89	136542100033	Ben	Ong	III	W.	Male	2020-02-02	Mila Ong	nutrisight1@mailto.plus	18 Osmena St, Brgy Mabiga, Mabalacat City	2026-09-14 00:03:28	2026-09-14 18:49:25	09174443333
90	136542100034	Meg	Chua	\N	X.	Female	2020-02-03	Nando Chua	nutrisight1@mailto.plus	29 Quezon St, Brgy Mabiga, Mabalacat City	2026-09-14 00:04:29	2026-09-14 18:49:51	09174445555
91	136542100035	Vic	Uy	\N	Y.	Male	2020-02-05	Olga Uy	nutrisight1@mailto.plus	30 Laurel St, Brgy Mabiga, Mabalacat City	2026-09-14 00:05:25	2026-09-14 18:50:04	09174446666
98	28477422	Richard	Santos	\N	Berto	Male	2004-08-14	Maria Clark	mariaclark@gmail.com	1234mabalacatcity	2026-09-16 13:29:36	2026-09-16 13:29:36	09123456789
97	6	chin	lee	\N	\N	Male	2026-09-02	chan	santos.richarddavid@gmail.com	Angeles	2026-09-16 11:16:03	2026-09-16 14:00:58	09631212312
99	23142343423	Janrey	Nicdao	\N	\N	Male	2026-09-18	Elena Garcia	zora20.pro@gmail.com	12313Mabalacat	2026-09-18 10:46:29	2026-09-18 10:46:29	09351111111
100	471414141	Matthew	Magtoto	\N	\N	Male	2026-09-18	Dracula	moltenterabyte@gmail.com	45454	2026-09-18 13:44:08	2026-09-18 13:44:08	09913201460
101	4864648645	Richard	Mungcal	\N	\N	Male	2026-09-18	parent	nicdaojanrey07@gmail.com	123123	2026-09-18 15:59:54	2026-09-18 15:59:54	2552525
102	100123456789	Juan	Santos	\N	\N	Male	2026-09-02	Maria Santos	maria.santos@example.com	123 Sample St., Brgy. Balibago, Angeles City	2026-09-20 16:34:06	2026-09-20 16:34:06	09170000001
103	100234567891	Angela	Dela Cruz	\N	\N	Female	2026-09-02	Roberto Dela Cruz	roberto.delacruz@example.com	45 Mabini St., Brgy. Pulung Cacutud, Angeles City	2026-09-20 16:36:18	2026-09-20 16:36:18	09170000002
104	100345678912	Daniel	Garcia	\N	\N	Male	2026-09-01	Elena Garcia	elena.garcia@example.com	78 Rizal St., Brgy. Cutcut, Angeles City	2026-09-20 16:38:33	2026-09-20 16:38:33	09170000003
105	100456789123	Sofia	Reyes	\N	\N	Female	2026-09-17	Roberto Reyes	roberto.reyes@example.com	16 Sampaguita St., Brgy. Anunas, Angeles City	2026-09-20 16:39:52	2026-09-20 16:39:52	09170000004
106	100567891234	Carlo	Mendoza	\N	\N	Male	2026-09-04	Teresa Mendoza	teresa.mendoza@example.com	92 MacArthur Hwy., Brgy. Balibago, Angeles City	2026-09-20 16:40:58	2026-09-20 16:40:58	09170000005
107	100678912345	Mia	Aquino	\N	\N	Female	2026-09-04	Daniel Aquino	daniel.aquino@example.com	31 Narra St., Brgy. Pampang, Angeles City	2026-09-20 16:42:03	2026-09-20 16:42:03	09170000006
108	100789123456	LIam	Navarro	\N	\N	Male	2026-09-04	Carla Navarro	carla.navarro@example.com	54 Acacia St., Brgy. Sapang Bato, Angeles City	2026-09-20 16:43:18	2026-09-20 16:43:18	09170000007
109	100891234567	Ella	Florez	\N	\N	Female	2026-09-03	Mark Flores	mark.flores@example.com	27 Dahlia St., Brgy. Lourdes North West, Angeles City	2026-09-20 16:44:20	2026-09-20 16:44:20	09170000008
110	100912345678	Noah	Castillo	\N	\N	Male	2026-09-04	Linda Castillo	linda.castillo@example.com	63 Bonifacio St., Brgy. Sto. Domingo, Angeles City	2026-09-20 16:45:37	2026-09-20 16:45:37	09170000009
111	101123456789	Chloe	Ramos	\N	\N	Female	2026-09-02	Victor Ramos	victor.ramos@example.com	18 Luna St., Brgy. Balibago, Angeles City	2026-09-20 16:47:32	2026-09-20 16:47:32	09170000010
112	112233445566	Cacalde	Nicdao	\N	\N	Male	2020-01-01	Miss Nicdao	nutrisight1@mailto.plus	Camachiles, Mabalacat City, Pampanga	2026-09-20 17:35:41	2026-09-20 17:35:41	09913201460
113	106088100100	Juan	Tamad	\N	\N	Male	2019-01-01	John Lazy	nutrisight1@mailto.plus	Slacker Street, Mabalacat City, Pampanga	2026-09-20 19:55:43	2026-09-20 19:55:43	09913201459
114	101123456754	Chad	Santos	\N	\N	Male	2026-09-03	Maria Santos	santos.richarddavid@gmail.com	121213 Angeles	2026-09-20 20:05:16	2026-09-20 20:05:39	09670909111
115	106088100111	Maria	Masipag	\N	\N	Female	2019-02-01	Mary Productive	moltenterabyte@gmail.com	Masipag Street, Angeles City, Pampanga	2026-09-20 20:17:10	2026-09-20 20:17:10	09237890127
116	17878792278	Chad. 2	Santos	Richard Santos	\N	Male	2026-09-17	Maria	santos.richarddavid@gmail.com	Angeles	2026-09-20 20:27:24	2026-09-20 20:27:24	0999786889
117	136542100000	Juan	Dela Cruz	jr	Santos	Male	2018-10-09	eqe	santos.richarddavid@gmail.com	121212Angeles City	2026-09-22 19:39:53	2026-09-22 19:40:55	09671212121
60	136542100004	Bea	Mendoza	\N	T.	Female	2020-01-04	Pedro Mendoza	moltenterabyte@gmail.com	78 Bonifacio St, Brgy Mabiga, Mabalacat City	2026-09-13 22:58:32	2026-09-22 23:31:43	09171115555
118	12121212121	Mark	Santos	\N	\N	Male	2020-01-27	Maria Santos	santos.richarddavid@gmail.com	1212Angeles City	2026-09-23 13:52:09	2026-09-23 13:53:21	09671212121
119	10000000000100	John	Doe	\N	\N	Male	2020-06-01	Jane Doe	moltenterabyte@gmail.com	123 Angeles City, Pampanga	2026-09-25 09:39:17	2026-09-25 09:39:17	09913201457
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, name, email, email_verified_at, password, sex, role, birthdate, "position", advisory_grade_level, advisory_section, deped_id, is_active, deleted_at, remember_token, created_at, updated_at, first_name, middle_name, last_name, name_extension) FROM stdin;
31	Encoder U. One	moltenterabyte@gmail.com	\N	$2y$12$yzY6QCYIQhK48/gwHiv/qOewAsL.OXnfnCRG4YEkOQYPctn/d0jvi	Male	encoder	2000-01-01	Teacher I	2	B	100010	t	\N	\N	2026-09-10 13:50:02	2026-09-21 07:55:50	Encoder	User	One	\N
33	Encoder U. 3	encoder3@nutrisight.test	\N	$2y$12$bN908bEtRhoUEH.ss9pQ.eNG.dUvWOaJqxb3RzCBTgOldFwasrTte	Male	encoder	2000-03-01	Teacher III	1	A	100006	t	\N	sxOH5h61XAb8sfGhAK4CDxdZiefPr9yo6DqDXlVN43Sxd42Yx59farLcKMIZ	2026-09-10 13:52:15	2026-09-14 01:01:30	Encoder	User	3	\N
26	Encoder User One	encoder@nutrisight.test	\N	$2y$12$hVafs1iVkdk6Pjv.67yl..fM123/jteMu3F4.6tfp2sYvAvL9U0Nm	Female	encoder	1995-01-01	Teacher I	1	A	100001	t	\N	ZHl03BwVIr2tzQJjb1xax6kp3wGRbQVZMPhJJjzaF23sx4zytUMFggrd0EZH	2026-09-09 22:17:26	2026-09-20 22:48:46	Encoder	\N	User One	\N
27	Admin User	admin@nutrisight.test	\N	$2y$12$19uTY9FJg8y8fVUpmGwWted5raqTaSUOykDGCH0UDQvrAnBGA/aTa	Male	admin	1990-01-01	Master Teacher I	\N	\N	100002	t	\N	rUnwpubX6ZJwKYdRNPa1jyPRp0NobQoFHdI5D2QUsukZPTiCxiCSuKFBfLgp	2026-09-09 22:17:27	2026-09-20 22:06:17	Admin	\N	User	\N
28	Super A. User	superadmin@nutrisight.test	\N	$2y$12$.6Wie9iA5YVa.UOsMtBXTumQ.qLtJZo7K0wrx7PeU86pBf7JXmLny	Male	super_admin	1985-01-01	Master Teacher II			100003	t	\N	HL6luddr5EkeNGh084WviylA3DZUy428gUwHeDQ1NkfDGGdbDxQuL09V6rRR	2026-09-09 22:17:27	2026-09-09 22:17:27	Super	Admin	User	\N
32	Encoder U. 2	encoder2@nutrisight.test	\N	$2y$12$0DsbesTQS.QMYmylmQVtreTHoY82OTiiDQeT5CB7fXni9lVB5OPyu	Male	encoder	2000-02-01	Teacher II	2	B	100005	t	\N	\N	2026-09-10 13:51:08	2026-09-14 01:03:25	Encoder	User	2	\N
\.


--
-- Data for Name: schema_migrations; Type: TABLE DATA; Schema: realtime; Owner: supabase_admin
--

COPY realtime.schema_migrations (version, inserted_at) FROM stdin;
20211116024918	2026-07-03 03:36:18
20211116045059	2026-07-03 03:36:18
20211116050929	2026-07-03 03:36:18
20211116051442	2026-07-03 03:36:18
20211116212300	2026-07-03 03:36:18
20211116213355	2026-07-03 03:36:18
20211116213934	2026-07-03 03:36:18
20211116214523	2026-07-03 03:36:18
20211122062447	2026-07-03 03:36:18
20211124070109	2026-07-03 03:36:18
20211202204204	2026-07-03 03:36:18
20211202204605	2026-07-03 03:36:18
20211210212804	2026-07-03 03:36:18
20211228014915	2026-07-03 03:36:18
20220107221237	2026-07-03 03:36:18
20220228202821	2026-07-03 03:36:18
20220312004840	2026-07-03 03:36:18
20220603231003	2026-07-03 03:36:18
20220603232444	2026-07-03 03:36:18
20220615214548	2026-07-03 03:36:18
20220712093339	2026-07-03 03:36:18
20220908172859	2026-07-03 03:36:18
20220916233421	2026-07-03 03:36:18
20230119133233	2026-07-03 03:36:18
20230128025114	2026-07-03 03:36:18
20230128025212	2026-07-03 03:36:18
20230227211149	2026-07-03 03:36:18
20230228184745	2026-07-03 03:36:18
20230308225145	2026-07-03 03:36:18
20230328144023	2026-07-03 03:36:18
20231018144023	2026-07-03 03:36:18
20231204144023	2026-07-03 03:36:18
20231204144024	2026-07-03 03:36:18
20231204144025	2026-07-03 03:36:18
20240108234812	2026-07-03 03:36:18
20240109165339	2026-07-03 03:36:18
20240227174441	2026-07-03 03:36:18
20240311171622	2026-07-03 03:36:18
20240321100241	2026-07-03 03:36:18
20240401105812	2026-07-03 03:36:18
20240418121054	2026-07-03 03:36:18
20240523004032	2026-07-03 03:36:18
20240618124746	2026-07-03 03:36:18
20240801235015	2026-07-03 03:36:18
20240805133720	2026-07-03 03:36:18
20240827160934	2026-07-03 03:36:18
20240919163303	2026-07-03 03:36:18
20240919163305	2026-07-03 03:36:18
20241019105805	2026-07-03 03:36:18
20241030150047	2026-07-03 03:36:18
20241108114728	2026-07-03 03:36:18
20241121104152	2026-07-03 03:36:18
20241130184212	2026-07-03 03:36:18
20241220035512	2026-07-03 03:36:18
20241220123912	2026-07-03 03:36:18
20241224161212	2026-07-03 03:36:19
20250107150512	2026-07-03 03:36:19
20250110162412	2026-07-03 03:36:19
20250123174212	2026-07-03 03:36:19
20250128220012	2026-07-03 03:36:19
20250506224012	2026-07-03 03:36:19
20250523164012	2026-07-03 03:36:19
20250714121412	2026-07-03 03:36:19
20250905041441	2026-07-03 03:36:19
20251103001201	2026-07-03 03:36:19
20251120212548	2026-07-03 03:36:19
20251120215549	2026-07-03 03:36:19
20260218120000	2026-07-03 03:36:19
20260326120000	2026-07-03 03:36:19
20260514120000	2026-07-03 03:36:19
20260527120000	2026-07-03 03:36:19
20260528120000	2026-07-03 03:36:19
20260603120000	2026-07-03 03:36:19
20260605120000	2026-07-03 03:36:19
20260606110000	2026-07-03 03:36:19
20260616120000	2026-07-03 03:36:19
20260624120000	2026-07-03 03:36:19
20260626120000	2026-07-03 03:36:19
20260706120000	2026-07-11 23:41:37
20260707120000	2026-07-21 05:37:26
20260709120000	2026-07-21 05:37:26
20260714120000	2026-09-04 12:13:49
20260827120000	2026-09-16 02:51:58
20260914120000	2026-09-22 10:20:12
20260916120000	2026-09-22 10:20:12
\.


--
-- Data for Name: subscription; Type: TABLE DATA; Schema: realtime; Owner: supabase_realtime_admin
--

COPY realtime.subscription (id, subscription_id, entity, filters, claims, created_at, action_filter, selected_columns) FROM stdin;
\.


--
-- Data for Name: buckets; Type: TABLE DATA; Schema: storage; Owner: supabase_storage_admin
--

COPY storage.buckets (id, name, owner, created_at, updated_at, public, avif_autodetection, file_size_limit, allowed_mime_types, owner_id, type, versioning_status, lifecycle_configuration, lifecycle_configuration_generation) FROM stdin;
\.


--
-- Data for Name: buckets_analytics; Type: TABLE DATA; Schema: storage; Owner: supabase_storage_admin
--

COPY storage.buckets_analytics (name, type, format, created_at, updated_at, id, deleted_at) FROM stdin;
\.


--
-- Data for Name: buckets_vectors; Type: TABLE DATA; Schema: storage; Owner: supabase_storage_admin
--

COPY storage.buckets_vectors (id, type, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: storage; Owner: supabase_storage_admin
--

COPY storage.migrations (id, name, hash, executed_at) FROM stdin;
0	create-migrations-table	e18db593bcde2aca2a408c4d1100f6abba2195df	2026-07-03 01:14:08.290567
1	initialmigration	6ab16121fbaa08bbd11b712d05f358f9b555d777	2026-07-03 01:14:08.33598
2	storage-schema	f6a1fa2c93cbcd16d4e487b362e45fca157a8dbd	2026-07-03 01:14:08.341925
3	pathtoken-column	2cb1b0004b817b29d5b0a971af16bafeede4b70d	2026-07-03 01:14:08.366018
4	add-migrations-rls	427c5b63fe1c5937495d9c635c263ee7a5905058	2026-07-03 01:14:08.382653
5	add-size-functions	79e081a1455b63666c1294a440f8ad4b1e6a7f84	2026-07-03 01:14:08.388334
6	change-column-name-in-get-size	ded78e2f1b5d7e616117897e6443a925965b30d2	2026-07-03 01:14:08.394021
7	add-rls-to-buckets	e7e7f86adbc51049f341dfe8d30256c1abca17aa	2026-07-03 01:14:08.399223
8	add-public-to-buckets	fd670db39ed65f9d08b01db09d6202503ca2bab3	2026-07-03 01:14:08.404695
9	fix-search-function	af597a1b590c70519b464a4ab3be54490712796b	2026-07-03 01:14:08.409603
10	search-files-search-function	b595f05e92f7e91211af1bbfe9c6a13bb3391e16	2026-07-03 01:14:08.414718
11	add-trigger-to-auto-update-updated_at-column	7425bdb14366d1739fa8a18c83100636d74dcaa2	2026-07-03 01:14:08.420506
12	add-automatic-avif-detection-flag	8e92e1266eb29518b6a4c5313ab8f29dd0d08df9	2026-07-03 01:14:08.426022
13	add-bucket-custom-limits	cce962054138135cd9a8c4bcd531598684b25e7d	2026-07-03 01:14:08.430971
14	use-bytes-for-max-size	941c41b346f9802b411f06f30e972ad4744dad27	2026-07-03 01:14:08.43599
15	add-can-insert-object-function	934146bc38ead475f4ef4b555c524ee5d66799e5	2026-07-03 01:14:08.462083
16	add-version	76debf38d3fd07dcfc747ca49096457d95b1221b	2026-07-03 01:14:08.467227
17	drop-owner-foreign-key	f1cbb288f1b7a4c1eb8c38504b80ae2a0153d101	2026-07-03 01:14:08.472045
18	add_owner_id_column_deprecate_owner	e7a511b379110b08e2f214be852c35414749fe66	2026-07-03 01:14:08.476735
19	alter-default-value-objects-id	02e5e22a78626187e00d173dc45f58fa66a4f043	2026-07-03 01:14:08.483333
20	list-objects-with-delimiter	cd694ae708e51ba82bf012bba00caf4f3b6393b7	2026-07-03 01:14:08.490582
21	s3-multipart-uploads	8c804d4a566c40cd1e4cc5b3725a664a9303657f	2026-07-03 01:14:08.498461
22	s3-multipart-uploads-big-ints	9737dc258d2397953c9953d9b86920b8be0cdb73	2026-07-03 01:14:08.515671
23	optimize-search-function	9d7e604cddc4b56a5422dc68c9313f4a1b6f132c	2026-07-03 01:14:08.525926
24	operation-function	8312e37c2bf9e76bbe841aa5fda889206d2bf8aa	2026-07-03 01:14:08.535546
25	custom-metadata	d974c6057c3db1c1f847afa0e291e6165693b990	2026-07-03 01:14:08.541166
26	objects-prefixes	215cabcb7f78121892a5a2037a09fedf9a1ae322	2026-07-03 01:14:08.546165
27	search-v2	859ba38092ac96eb3964d83bf53ccc0b141663a6	2026-07-03 01:14:08.552364
28	object-bucket-name-sorting	c73a2b5b5d4041e39705814fd3a1b95502d38ce4	2026-07-03 01:14:08.557038
29	create-prefixes	ad2c1207f76703d11a9f9007f821620017a66c21	2026-07-03 01:14:08.561524
30	update-object-levels	2be814ff05c8252fdfdc7cfb4b7f5c7e17f0bed6	2026-07-03 01:14:08.566301
31	objects-level-index	b40367c14c3440ec75f19bbce2d71e914ddd3da0	2026-07-03 01:14:08.571194
32	backward-compatible-index-on-objects	e0c37182b0f7aee3efd823298fb3c76f1042c0f7	2026-07-03 01:14:08.575536
33	backward-compatible-index-on-prefixes	b480e99ed951e0900f033ec4eb34b5bdcb4e3d49	2026-07-03 01:14:08.581011
34	optimize-search-function-v1	ca80a3dc7bfef894df17108785ce29a7fc8ee456	2026-07-03 01:14:08.585443
35	add-insert-trigger-prefixes	458fe0ffd07ec53f5e3ce9df51bfdf4861929ccc	2026-07-03 01:14:08.589839
36	optimise-existing-functions	6ae5fca6af5c55abe95369cd4f93985d1814ca8f	2026-07-03 01:14:08.594203
37	add-bucket-name-length-trigger	3944135b4e3e8b22d6d4cbb568fe3b0b51df15c1	2026-07-03 01:14:08.598476
38	iceberg-catalog-flag-on-buckets	02716b81ceec9705aed84aa1501657095b32e5c5	2026-07-03 01:14:08.608897
39	add-search-v2-sort-support	6706c5f2928846abee18461279799ad12b279b78	2026-07-03 01:14:08.621113
40	fix-prefix-race-conditions-optimized	7ad69982ae2d372b21f48fc4829ae9752c518f6b	2026-07-03 01:14:08.625522
41	add-object-level-update-trigger	07fcf1a22165849b7a029deed059ffcde08d1ae0	2026-07-03 01:14:08.62996
42	rollback-prefix-triggers	771479077764adc09e2ea2043eb627503c034cd4	2026-07-03 01:14:08.635673
43	fix-object-level	84b35d6caca9d937478ad8a797491f38b8c2979f	2026-07-03 01:14:08.640082
44	vector-bucket-type	99c20c0ffd52bb1ff1f32fb992f3b351e3ef8fb3	2026-07-03 01:14:08.646475
45	vector-buckets	049e27196d77a7cb76497a85afae669d8b230953	2026-07-03 01:14:08.651384
46	buckets-objects-grants	fedeb96d60fefd8e02ab3ded9fbde05632f84aed	2026-07-03 01:14:08.662029
47	iceberg-table-metadata	649df56855c24d8b36dd4cc1aeb8251aa9ad42c2	2026-07-03 01:14:08.668982
48	iceberg-catalog-ids	e0e8b460c609b9999ccd0df9ad14294613eed939	2026-07-03 01:14:08.673871
49	buckets-objects-grants-postgres	072b1195d0d5a2f888af6b2302a1938dd94b8b3d	2026-07-03 01:14:08.690133
50	search-v2-optimised	6323ac4f850aa14e7387eb32102869578b5bd478	2026-07-03 01:14:08.695177
51	index-backward-compatible-search	2ee395d433f76e38bcd3856debaf6e0e5b674011	2026-07-03 01:14:09.244442
52	drop-not-used-indexes-and-functions	5cc44c8696749ac11dd0dc37f2a3802075f3a171	2026-07-03 01:14:09.246611
53	drop-index-lower-name	d0cb18777d9e2a98ebe0bc5cc7a42e57ebe41854	2026-07-03 01:14:09.258286
54	drop-index-object-level	6289e048b1472da17c31a7eba1ded625a6457e67	2026-07-03 01:14:09.261194
55	prevent-direct-deletes	262a4798d5e0f2e7c8970232e03ce8be695d5819	2026-07-03 01:14:09.26311
56	fix-optimized-search-function	b823ed1e418101032fa01374edc9a436e54e3ed4	2026-07-03 01:14:09.268762
57	s3-multipart-uploads-metadata	f127886e00d1b374fadbc7c6b31e09336aad5287	2026-07-03 01:14:09.289561
58	operation-ergonomics	00ca5d483b3fe0d522133d9002ccc5df98365120	2026-07-03 01:14:09.295269
59	drop-unused-functions	38456f13e39691c2bbb4b5151d0d1cdbabd4a8c4	2026-07-03 01:14:09.301558
60	optimize-existing-functions-again	db35e1c91a9201e59f4fef8d972c2f277d68b157	2026-07-03 01:14:09.306815
61	mark-filename-immutable	fe0096517ae9d60aaec1d110172ba9036dc66bb7	2026-08-15 15:19:37.610419
62	object-versioning-core	0b855f00ff3be0bfca91efee02a9858912491a9a	2026-08-27 04:23:08.567448
63	fix-search-name-relative-to-prefix	c7485e417624f795ce8bb2da21927f48e088904d	2026-08-27 04:23:08.61763
64	fix-search-by-timestamp-sqli	0af424ecd388a39bb1645184b222185a12149675	2026-08-27 04:23:08.632055
66	objects-current-version-index	191466c93aa2c46a00e36505577c5fcab8d7cb4b	2026-09-08 00:21:10.940668
67	objects-null-version-index	15bfe8c35b66642b6c78ba60060fa8793bd2207a	2026-09-08 00:21:10.949585
65	objects-key-version-index	da319c4b89ba800ce795d1b699f3a70675138058	2026-09-08 00:21:10.93124
68	bucket-lifecycle-configuration	3c08f6f889922f399519722a932b51007c11bebc	2026-09-22 10:20:18.486425
69	validate-bucket-lifecycle-constraints	4febacaaaa0e61e2b783bef081fe03a287e65eb3	2026-09-22 10:20:18.52959
70	list-objects-with-versions	5c17c3777616cd8d7b18b82835525fa3205af57b	2026-09-22 10:20:18.533423
71	objects-delete-marker-index	6d14858e66c66f8d6accf8a2630aefd1527fddba	2026-09-22 10:20:18.580593
72	drop-bucketid-objname-index	302beb09e1b469d7d4db19566f2389d280b64aa3	2026-09-22 10:20:18.592467
\.


--
-- Data for Name: objects; Type: TABLE DATA; Schema: storage; Owner: supabase_storage_admin
--

COPY storage.objects (id, bucket_id, name, owner, created_at, updated_at, last_accessed_at, metadata, version, owner_id, user_metadata, archived_at, is_delete_marker, is_versioned) FROM stdin;
\.


--
-- Data for Name: s3_multipart_uploads; Type: TABLE DATA; Schema: storage; Owner: supabase_storage_admin
--

COPY storage.s3_multipart_uploads (id, in_progress_size, upload_signature, bucket_id, key, version, owner_id, created_at, user_metadata, metadata) FROM stdin;
\.


--
-- Data for Name: s3_multipart_uploads_parts; Type: TABLE DATA; Schema: storage; Owner: supabase_storage_admin
--

COPY storage.s3_multipart_uploads_parts (id, upload_id, size, part_number, bucket_id, key, etag, owner_id, version, created_at) FROM stdin;
\.


--
-- Data for Name: vector_indexes; Type: TABLE DATA; Schema: storage; Owner: supabase_storage_admin
--

COPY storage.vector_indexes (id, name, bucket_id, data_type, dimension, distance_metric, metadata_configuration, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: secrets; Type: TABLE DATA; Schema: vault; Owner: supabase_admin
--

COPY vault.secrets (id, name, description, secret, key_id, nonce, created_at, updated_at) FROM stdin;
\.


--
-- Name: refresh_tokens_id_seq; Type: SEQUENCE SET; Schema: auth; Owner: supabase_auth_admin
--

SELECT pg_catalog.setval('auth.refresh_tokens_id_seq', 1, false);


--
-- Name: attendance_report_months_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.attendance_report_months_id_seq', 3, true);


--
-- Name: attendance_report_sections_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.attendance_report_sections_id_seq', 1, false);


--
-- Name: audit_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.audit_logs_id_seq', 544, true);


--
-- Name: enrollments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.enrollments_id_seq', 119, true);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.jobs_id_seq', 13, true);


--
-- Name: meal_plans_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.meal_plans_id_seq', 24, true);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 53, true);


--
-- Name: nutrition_measurements_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.nutrition_measurements_id_seq', 188, true);


--
-- Name: report_period_rows_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.report_period_rows_id_seq', 2224, true);


--
-- Name: report_periods_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.report_periods_id_seq', 6, true);


--
-- Name: sbfp_parent_approval_requests_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.sbfp_parent_approval_requests_id_seq', 10, true);


--
-- Name: sbfp_participants_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.sbfp_participants_id_seq', 119, true);


--
-- Name: school_year_user_records_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.school_year_user_records_id_seq', 24, true);


--
-- Name: school_years_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.school_years_id_seq', 8, true);


--
-- Name: student_attendance_records_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.student_attendance_records_id_seq', 294, true);


--
-- Name: student_feeding_records_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.student_feeding_records_id_seq', 1, false);


--
-- Name: students_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.students_id_seq', 119, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 33, true);


--
-- Name: subscription_id_seq; Type: SEQUENCE SET; Schema: realtime; Owner: supabase_realtime_admin
--

SELECT pg_catalog.setval('realtime.subscription_id_seq', 1, false);


--
-- Name: mfa_amr_claims amr_id_pk; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_amr_claims
    ADD CONSTRAINT amr_id_pk PRIMARY KEY (id);


--
-- Name: audit_log_entries audit_log_entries_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.audit_log_entries
    ADD CONSTRAINT audit_log_entries_pkey PRIMARY KEY (id);


--
-- Name: custom_oauth_providers custom_oauth_providers_identifier_key; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.custom_oauth_providers
    ADD CONSTRAINT custom_oauth_providers_identifier_key UNIQUE (identifier);


--
-- Name: custom_oauth_providers custom_oauth_providers_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.custom_oauth_providers
    ADD CONSTRAINT custom_oauth_providers_pkey PRIMARY KEY (id);


--
-- Name: flow_state flow_state_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.flow_state
    ADD CONSTRAINT flow_state_pkey PRIMARY KEY (id);


--
-- Name: identities identities_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.identities
    ADD CONSTRAINT identities_pkey PRIMARY KEY (id);


--
-- Name: identities identities_provider_id_provider_unique; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.identities
    ADD CONSTRAINT identities_provider_id_provider_unique UNIQUE (provider_id, provider);


--
-- Name: instances instances_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.instances
    ADD CONSTRAINT instances_pkey PRIMARY KEY (id);


--
-- Name: mfa_amr_claims mfa_amr_claims_session_id_authentication_method_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_amr_claims
    ADD CONSTRAINT mfa_amr_claims_session_id_authentication_method_pkey UNIQUE (session_id, authentication_method);


--
-- Name: mfa_challenges mfa_challenges_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_challenges
    ADD CONSTRAINT mfa_challenges_pkey PRIMARY KEY (id);


--
-- Name: mfa_factors mfa_factors_last_challenged_at_key; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_factors
    ADD CONSTRAINT mfa_factors_last_challenged_at_key UNIQUE (last_challenged_at);


--
-- Name: mfa_factors mfa_factors_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_factors
    ADD CONSTRAINT mfa_factors_pkey PRIMARY KEY (id);


--
-- Name: mfa_recovery_code_sets mfa_recovery_code_sets_mfa_factor_id_key; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_recovery_code_sets
    ADD CONSTRAINT mfa_recovery_code_sets_mfa_factor_id_key UNIQUE (mfa_factor_id);


--
-- Name: mfa_recovery_code_sets mfa_recovery_code_sets_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_recovery_code_sets
    ADD CONSTRAINT mfa_recovery_code_sets_pkey PRIMARY KEY (id);


--
-- Name: mfa_recovery_code_sets mfa_recovery_code_sets_user_id_key; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_recovery_code_sets
    ADD CONSTRAINT mfa_recovery_code_sets_user_id_key UNIQUE (user_id);


--
-- Name: mfa_recovery_codes mfa_recovery_codes_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_recovery_codes
    ADD CONSTRAINT mfa_recovery_codes_pkey PRIMARY KEY (id);


--
-- Name: oauth_authorizations oauth_authorizations_authorization_code_key; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.oauth_authorizations
    ADD CONSTRAINT oauth_authorizations_authorization_code_key UNIQUE (authorization_code);


--
-- Name: oauth_authorizations oauth_authorizations_authorization_id_key; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.oauth_authorizations
    ADD CONSTRAINT oauth_authorizations_authorization_id_key UNIQUE (authorization_id);


--
-- Name: oauth_authorizations oauth_authorizations_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.oauth_authorizations
    ADD CONSTRAINT oauth_authorizations_pkey PRIMARY KEY (id);


--
-- Name: oauth_client_states oauth_client_states_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.oauth_client_states
    ADD CONSTRAINT oauth_client_states_pkey PRIMARY KEY (id);


--
-- Name: oauth_clients oauth_clients_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.oauth_clients
    ADD CONSTRAINT oauth_clients_pkey PRIMARY KEY (id);


--
-- Name: oauth_consents oauth_consents_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.oauth_consents
    ADD CONSTRAINT oauth_consents_pkey PRIMARY KEY (id);


--
-- Name: oauth_consents oauth_consents_user_client_unique; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.oauth_consents
    ADD CONSTRAINT oauth_consents_user_client_unique UNIQUE (user_id, client_id);


--
-- Name: one_time_tokens one_time_tokens_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.one_time_tokens
    ADD CONSTRAINT one_time_tokens_pkey PRIMARY KEY (id);


--
-- Name: refresh_tokens refresh_tokens_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.refresh_tokens
    ADD CONSTRAINT refresh_tokens_pkey PRIMARY KEY (id);


--
-- Name: refresh_tokens refresh_tokens_token_unique; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.refresh_tokens
    ADD CONSTRAINT refresh_tokens_token_unique UNIQUE (token);


--
-- Name: saml_providers saml_providers_entity_id_key; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.saml_providers
    ADD CONSTRAINT saml_providers_entity_id_key UNIQUE (entity_id);


--
-- Name: saml_providers saml_providers_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.saml_providers
    ADD CONSTRAINT saml_providers_pkey PRIMARY KEY (id);


--
-- Name: saml_relay_states saml_relay_states_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.saml_relay_states
    ADD CONSTRAINT saml_relay_states_pkey PRIMARY KEY (id);


--
-- Name: schema_migrations schema_migrations_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.schema_migrations
    ADD CONSTRAINT schema_migrations_pkey PRIMARY KEY (version);


--
-- Name: scim_tokens scim_tokens_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.scim_tokens
    ADD CONSTRAINT scim_tokens_pkey PRIMARY KEY (id);


--
-- Name: scim_users scim_users_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.scim_users
    ADD CONSTRAINT scim_users_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: sso_domains sso_domains_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.sso_domains
    ADD CONSTRAINT sso_domains_pkey PRIMARY KEY (id);


--
-- Name: sso_providers sso_providers_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.sso_providers
    ADD CONSTRAINT sso_providers_pkey PRIMARY KEY (id);


--
-- Name: users users_phone_key; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.users
    ADD CONSTRAINT users_phone_key UNIQUE (phone);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: webauthn_challenges webauthn_challenges_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.webauthn_challenges
    ADD CONSTRAINT webauthn_challenges_pkey PRIMARY KEY (id);


--
-- Name: webauthn_credentials webauthn_credentials_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.webauthn_credentials
    ADD CONSTRAINT webauthn_credentials_pkey PRIMARY KEY (id);


--
-- Name: attendance_report_months attendance_report_months_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attendance_report_months
    ADD CONSTRAINT attendance_report_months_pkey PRIMARY KEY (id);


--
-- Name: attendance_report_months attendance_report_months_school_year_id_month_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attendance_report_months
    ADD CONSTRAINT attendance_report_months_school_year_id_month_unique UNIQUE (school_year_id, month);


--
-- Name: attendance_report_sections attendance_report_sections_attendance_report_month_id_grade_lev; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attendance_report_sections
    ADD CONSTRAINT attendance_report_sections_attendance_report_month_id_grade_lev UNIQUE (attendance_report_month_id, grade_level, section);


--
-- Name: attendance_report_sections attendance_report_sections_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attendance_report_sections
    ADD CONSTRAINT attendance_report_sections_pkey PRIMARY KEY (id);


--
-- Name: audit_logs audit_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.audit_logs
    ADD CONSTRAINT audit_logs_pkey PRIMARY KEY (id);


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
-- Name: enrollments enrollments_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.enrollments
    ADD CONSTRAINT enrollments_pkey PRIMARY KEY (id);


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
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: meal_plans meal_plans_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.meal_plans
    ADD CONSTRAINT meal_plans_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: nutrition_measurements nutrition_measurements_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.nutrition_measurements
    ADD CONSTRAINT nutrition_measurements_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: report_period_rows report_period_rows_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.report_period_rows
    ADD CONSTRAINT report_period_rows_pkey PRIMARY KEY (id);


--
-- Name: report_period_rows report_period_rows_report_period_id_grade_level_sex_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.report_period_rows
    ADD CONSTRAINT report_period_rows_report_period_id_grade_level_sex_unique UNIQUE (report_period_id, grade_level, sex);


--
-- Name: report_periods report_periods_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.report_periods
    ADD CONSTRAINT report_periods_pkey PRIMARY KEY (id);


--
-- Name: report_periods report_periods_school_year_id_measurement_period_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.report_periods
    ADD CONSTRAINT report_periods_school_year_id_measurement_period_unique UNIQUE (school_year_id, measurement_period);


--
-- Name: sbfp_parent_approval_requests sbfp_parent_approval_requests_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sbfp_parent_approval_requests
    ADD CONSTRAINT sbfp_parent_approval_requests_pkey PRIMARY KEY (id);


--
-- Name: sbfp_parent_approval_requests sbfp_parent_approval_requests_token_hash_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sbfp_parent_approval_requests
    ADD CONSTRAINT sbfp_parent_approval_requests_token_hash_unique UNIQUE (token_hash);


--
-- Name: sbfp_participants sbfp_participants_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sbfp_participants
    ADD CONSTRAINT sbfp_participants_pkey PRIMARY KEY (id);


--
-- Name: sbfp_participants sbfp_participants_profile_image_url_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sbfp_participants
    ADD CONSTRAINT sbfp_participants_profile_image_url_key UNIQUE (profile_image_url);


--
-- Name: school_year_user_records school_year_user_records_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.school_year_user_records
    ADD CONSTRAINT school_year_user_records_pkey PRIMARY KEY (id);


--
-- Name: school_year_user_records school_year_user_records_school_year_id_user_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.school_year_user_records
    ADD CONSTRAINT school_year_user_records_school_year_id_user_id_unique UNIQUE (school_year_id, user_id);


--
-- Name: school_years school_years_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.school_years
    ADD CONSTRAINT school_years_pkey PRIMARY KEY (id);


--
-- Name: school_years school_years_year_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.school_years
    ADD CONSTRAINT school_years_year_unique UNIQUE (year);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: student_attendance_records student_attendance_records_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.student_attendance_records
    ADD CONSTRAINT student_attendance_records_pkey PRIMARY KEY (id);


--
-- Name: student_feeding_records student_feeding_records_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.student_feeding_records
    ADD CONSTRAINT student_feeding_records_pkey PRIMARY KEY (id);


--
-- Name: students students_lrn_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.students
    ADD CONSTRAINT students_lrn_unique UNIQUE (lrn);


--
-- Name: students students_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.students
    ADD CONSTRAINT students_pkey PRIMARY KEY (id);


--
-- Name: users users_deped_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_deped_id_unique UNIQUE (deped_id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: messages messages_payload_exclusive; Type: CHECK CONSTRAINT; Schema: realtime; Owner: supabase_realtime_admin
--

ALTER TABLE realtime.messages
    ADD CONSTRAINT messages_payload_exclusive CHECK (((payload IS NULL) OR (binary_payload IS NULL))) NOT VALID;


--
-- Name: messages messages_pkey; Type: CONSTRAINT; Schema: realtime; Owner: supabase_realtime_admin
--

ALTER TABLE ONLY realtime.messages
    ADD CONSTRAINT messages_pkey PRIMARY KEY (id, inserted_at);


--
-- Name: subscription pk_subscription; Type: CONSTRAINT; Schema: realtime; Owner: supabase_realtime_admin
--

ALTER TABLE ONLY realtime.subscription
    ADD CONSTRAINT pk_subscription PRIMARY KEY (id);


--
-- Name: schema_migrations schema_migrations_pkey; Type: CONSTRAINT; Schema: realtime; Owner: supabase_admin
--

ALTER TABLE ONLY realtime.schema_migrations
    ADD CONSTRAINT schema_migrations_pkey PRIMARY KEY (version);


--
-- Name: buckets_analytics buckets_analytics_pkey; Type: CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.buckets_analytics
    ADD CONSTRAINT buckets_analytics_pkey PRIMARY KEY (id);


--
-- Name: buckets buckets_pkey; Type: CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.buckets
    ADD CONSTRAINT buckets_pkey PRIMARY KEY (id);


--
-- Name: buckets_vectors buckets_vectors_pkey; Type: CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.buckets_vectors
    ADD CONSTRAINT buckets_vectors_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_name_key; Type: CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.migrations
    ADD CONSTRAINT migrations_name_key UNIQUE (name);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: objects objects_pkey; Type: CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.objects
    ADD CONSTRAINT objects_pkey PRIMARY KEY (id);


--
-- Name: s3_multipart_uploads_parts s3_multipart_uploads_parts_pkey; Type: CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.s3_multipart_uploads_parts
    ADD CONSTRAINT s3_multipart_uploads_parts_pkey PRIMARY KEY (id);


--
-- Name: s3_multipart_uploads s3_multipart_uploads_pkey; Type: CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.s3_multipart_uploads
    ADD CONSTRAINT s3_multipart_uploads_pkey PRIMARY KEY (id);


--
-- Name: vector_indexes vector_indexes_pkey; Type: CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.vector_indexes
    ADD CONSTRAINT vector_indexes_pkey PRIMARY KEY (id);


--
-- Name: audit_logs_instance_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX audit_logs_instance_id_idx ON auth.audit_log_entries USING btree (instance_id);


--
-- Name: confirmation_token_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX confirmation_token_idx ON auth.users USING btree (confirmation_token) WHERE ((confirmation_token)::text !~ '^[0-9 ]*$'::text);


--
-- Name: custom_oauth_providers_created_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX custom_oauth_providers_created_at_idx ON auth.custom_oauth_providers USING btree (created_at);


--
-- Name: custom_oauth_providers_enabled_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX custom_oauth_providers_enabled_idx ON auth.custom_oauth_providers USING btree (enabled);


--
-- Name: custom_oauth_providers_identifier_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX custom_oauth_providers_identifier_idx ON auth.custom_oauth_providers USING btree (identifier);


--
-- Name: custom_oauth_providers_provider_type_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX custom_oauth_providers_provider_type_idx ON auth.custom_oauth_providers USING btree (provider_type);


--
-- Name: email_change_token_current_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX email_change_token_current_idx ON auth.users USING btree (email_change_token_current) WHERE ((email_change_token_current)::text !~ '^[0-9 ]*$'::text);


--
-- Name: email_change_token_new_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX email_change_token_new_idx ON auth.users USING btree (email_change_token_new) WHERE ((email_change_token_new)::text !~ '^[0-9 ]*$'::text);


--
-- Name: factor_id_created_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX factor_id_created_at_idx ON auth.mfa_factors USING btree (user_id, created_at);


--
-- Name: flow_state_created_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX flow_state_created_at_idx ON auth.flow_state USING btree (created_at DESC);


--
-- Name: identities_email_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX identities_email_idx ON auth.identities USING btree (email text_pattern_ops);


--
-- Name: INDEX identities_email_idx; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON INDEX auth.identities_email_idx IS 'Auth: Ensures indexed queries on the email column';


--
-- Name: identities_user_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX identities_user_id_idx ON auth.identities USING btree (user_id);


--
-- Name: idx_auth_code; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX idx_auth_code ON auth.flow_state USING btree (auth_code);


--
-- Name: idx_oauth_client_states_created_at; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX idx_oauth_client_states_created_at ON auth.oauth_client_states USING btree (created_at);


--
-- Name: idx_user_id_auth_method; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX idx_user_id_auth_method ON auth.flow_state USING btree (user_id, authentication_method);


--
-- Name: idx_users_created_at_desc; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX idx_users_created_at_desc ON auth.users USING btree (created_at DESC);


--
-- Name: idx_users_email; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX idx_users_email ON auth.users USING btree (email);


--
-- Name: idx_users_last_sign_in_at_desc; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX idx_users_last_sign_in_at_desc ON auth.users USING btree (last_sign_in_at DESC);


--
-- Name: idx_users_name; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX idx_users_name ON auth.users USING btree (((raw_user_meta_data ->> 'name'::text))) WHERE ((raw_user_meta_data ->> 'name'::text) IS NOT NULL);


--
-- Name: mfa_challenge_created_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX mfa_challenge_created_at_idx ON auth.mfa_challenges USING btree (created_at DESC);


--
-- Name: mfa_factors_user_friendly_name_unique; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX mfa_factors_user_friendly_name_unique ON auth.mfa_factors USING btree (friendly_name, user_id) WHERE (TRIM(BOTH FROM friendly_name) <> ''::text);


--
-- Name: mfa_factors_user_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX mfa_factors_user_id_idx ON auth.mfa_factors USING btree (user_id);


--
-- Name: mfa_recovery_codes_set_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX mfa_recovery_codes_set_id_idx ON auth.mfa_recovery_codes USING btree (mfa_recovery_code_set_id);


--
-- Name: oauth_auth_pending_exp_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX oauth_auth_pending_exp_idx ON auth.oauth_authorizations USING btree (expires_at) WHERE (status = 'pending'::auth.oauth_authorization_status);


--
-- Name: oauth_clients_deleted_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX oauth_clients_deleted_at_idx ON auth.oauth_clients USING btree (deleted_at);


--
-- Name: oauth_consents_active_client_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX oauth_consents_active_client_idx ON auth.oauth_consents USING btree (client_id) WHERE (revoked_at IS NULL);


--
-- Name: oauth_consents_active_user_client_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX oauth_consents_active_user_client_idx ON auth.oauth_consents USING btree (user_id, client_id) WHERE (revoked_at IS NULL);


--
-- Name: oauth_consents_user_order_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX oauth_consents_user_order_idx ON auth.oauth_consents USING btree (user_id, granted_at DESC);


--
-- Name: one_time_tokens_relates_to_hash_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX one_time_tokens_relates_to_hash_idx ON auth.one_time_tokens USING hash (relates_to);


--
-- Name: one_time_tokens_token_hash_hash_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX one_time_tokens_token_hash_hash_idx ON auth.one_time_tokens USING hash (token_hash);


--
-- Name: one_time_tokens_user_id_token_type_key; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX one_time_tokens_user_id_token_type_key ON auth.one_time_tokens USING btree (user_id, token_type);


--
-- Name: reauthentication_token_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX reauthentication_token_idx ON auth.users USING btree (reauthentication_token) WHERE ((reauthentication_token)::text !~ '^[0-9 ]*$'::text);


--
-- Name: recovery_token_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX recovery_token_idx ON auth.users USING btree (recovery_token) WHERE ((recovery_token)::text !~ '^[0-9 ]*$'::text);


--
-- Name: refresh_tokens_instance_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX refresh_tokens_instance_id_idx ON auth.refresh_tokens USING btree (instance_id);


--
-- Name: refresh_tokens_instance_id_user_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX refresh_tokens_instance_id_user_id_idx ON auth.refresh_tokens USING btree (instance_id, user_id);


--
-- Name: refresh_tokens_parent_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX refresh_tokens_parent_idx ON auth.refresh_tokens USING btree (parent);


--
-- Name: refresh_tokens_session_id_revoked_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX refresh_tokens_session_id_revoked_idx ON auth.refresh_tokens USING btree (session_id, revoked);


--
-- Name: refresh_tokens_updated_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX refresh_tokens_updated_at_idx ON auth.refresh_tokens USING btree (updated_at DESC);


--
-- Name: saml_providers_sso_provider_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX saml_providers_sso_provider_id_idx ON auth.saml_providers USING btree (sso_provider_id);


--
-- Name: saml_relay_states_created_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX saml_relay_states_created_at_idx ON auth.saml_relay_states USING btree (created_at DESC);


--
-- Name: saml_relay_states_for_email_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX saml_relay_states_for_email_idx ON auth.saml_relay_states USING btree (for_email);


--
-- Name: saml_relay_states_sso_provider_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX saml_relay_states_sso_provider_id_idx ON auth.saml_relay_states USING btree (sso_provider_id);


--
-- Name: scim_tokens_expires_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX scim_tokens_expires_at_idx ON auth.scim_tokens USING btree (expires_at);


--
-- Name: scim_tokens_revoked_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX scim_tokens_revoked_at_idx ON auth.scim_tokens USING btree (revoked_at);


--
-- Name: scim_tokens_sso_provider_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX scim_tokens_sso_provider_id_idx ON auth.scim_tokens USING btree (sso_provider_id);


--
-- Name: scim_tokens_token_hash_key; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX scim_tokens_token_hash_key ON auth.scim_tokens USING btree (token_hash);


--
-- Name: scim_users_created_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX scim_users_created_at_idx ON auth.scim_users USING btree (sso_provider_id, created_at, id) WHERE (deleted_at IS NULL);


--
-- Name: scim_users_deleted_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX scim_users_deleted_at_idx ON auth.scim_users USING btree (deleted_at);


--
-- Name: scim_users_external_id_key; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX scim_users_external_id_key ON auth.scim_users USING btree (sso_provider_id, external_id) WHERE ((external_id IS NOT NULL) AND (deleted_at IS NULL));


--
-- Name: scim_users_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX scim_users_id_idx ON auth.scim_users USING btree (sso_provider_id, id) WHERE (deleted_at IS NULL);


--
-- Name: scim_users_sso_provider_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX scim_users_sso_provider_id_idx ON auth.scim_users USING btree (sso_provider_id);


--
-- Name: scim_users_updated_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX scim_users_updated_at_idx ON auth.scim_users USING btree (sso_provider_id, updated_at, id) WHERE (deleted_at IS NULL);


--
-- Name: scim_users_user_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX scim_users_user_id_idx ON auth.scim_users USING btree (user_id);


--
-- Name: scim_users_user_name_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX scim_users_user_name_idx ON auth.scim_users USING btree (sso_provider_id, user_name COLLATE "C", id) WHERE (deleted_at IS NULL);


--
-- Name: scim_users_user_name_key; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX scim_users_user_name_key ON auth.scim_users USING btree (sso_provider_id, user_name) WHERE (deleted_at IS NULL);


--
-- Name: sessions_not_after_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX sessions_not_after_idx ON auth.sessions USING btree (not_after DESC);


--
-- Name: sessions_oauth_client_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX sessions_oauth_client_id_idx ON auth.sessions USING btree (oauth_client_id);


--
-- Name: sessions_user_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX sessions_user_id_idx ON auth.sessions USING btree (user_id);


--
-- Name: sso_domains_domain_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX sso_domains_domain_idx ON auth.sso_domains USING btree (lower(domain));


--
-- Name: sso_domains_sso_provider_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX sso_domains_sso_provider_id_idx ON auth.sso_domains USING btree (sso_provider_id);


--
-- Name: sso_providers_resource_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX sso_providers_resource_id_idx ON auth.sso_providers USING btree (lower(resource_id));


--
-- Name: sso_providers_resource_id_pattern_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX sso_providers_resource_id_pattern_idx ON auth.sso_providers USING btree (resource_id text_pattern_ops);


--
-- Name: unique_phone_factor_per_user; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX unique_phone_factor_per_user ON auth.mfa_factors USING btree (user_id, phone);


--
-- Name: user_id_created_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX user_id_created_at_idx ON auth.sessions USING btree (user_id, created_at);


--
-- Name: users_email_partial_key; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX users_email_partial_key ON auth.users USING btree (email) WHERE (is_sso_user = false);


--
-- Name: INDEX users_email_partial_key; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON INDEX auth.users_email_partial_key IS 'Auth: A partial unique index that applies only when is_sso_user is false';


--
-- Name: users_instance_id_email_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX users_instance_id_email_idx ON auth.users USING btree (instance_id, lower((email)::text));


--
-- Name: users_instance_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX users_instance_id_idx ON auth.users USING btree (instance_id);


--
-- Name: users_is_anonymous_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX users_is_anonymous_idx ON auth.users USING btree (is_anonymous);


--
-- Name: webauthn_challenges_expires_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX webauthn_challenges_expires_at_idx ON auth.webauthn_challenges USING btree (expires_at);


--
-- Name: webauthn_challenges_user_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX webauthn_challenges_user_id_idx ON auth.webauthn_challenges USING btree (user_id);


--
-- Name: webauthn_credentials_credential_id_key; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX webauthn_credentials_credential_id_key ON auth.webauthn_credentials USING btree (credential_id);


--
-- Name: webauthn_credentials_user_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX webauthn_credentials_user_id_idx ON auth.webauthn_credentials USING btree (user_id);


--
-- Name: cache_expiration_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX cache_expiration_index ON public.cache USING btree (expiration);


--
-- Name: cache_locks_expiration_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX cache_locks_expiration_index ON public.cache_locks USING btree (expiration);


--
-- Name: enrollments_school_year_id_grade_level_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX enrollments_school_year_id_grade_level_index ON public.enrollments USING btree (school_year_id, grade_level);


--
-- Name: failed_jobs_connection_queue_failed_at_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX failed_jobs_connection_queue_failed_at_index ON public.failed_jobs USING btree (connection, queue, failed_at);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: meal_plans_meal_date_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX meal_plans_meal_date_index ON public.meal_plans USING btree (meal_date);


--
-- Name: sbfp_parent_approval_requests_sbfp_participant_id_status_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sbfp_parent_approval_requests_sbfp_participant_id_status_index ON public.sbfp_parent_approval_requests USING btree (sbfp_participant_id, status);


--
-- Name: school_year_user_records_school_year_id_role_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX school_year_user_records_school_year_id_role_index ON public.school_year_user_records USING btree (school_year_id, role);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: student_attendance_records_attendance_date_status_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX student_attendance_records_attendance_date_status_index ON public.student_attendance_records USING btree (attendance_date, status);


--
-- Name: ix_realtime_subscription_entity; Type: INDEX; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE INDEX ix_realtime_subscription_entity ON realtime.subscription USING btree (entity);


--
-- Name: messages_inserted_at_topic_index; Type: INDEX; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE INDEX messages_inserted_at_topic_index ON ONLY realtime.messages USING btree (inserted_at DESC, topic) WHERE ((extension = 'broadcast'::text) AND (private IS TRUE));


--
-- Name: subscription_subscription_id_entity_filters_action_filter_selec; Type: INDEX; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE UNIQUE INDEX subscription_subscription_id_entity_filters_action_filter_selec ON realtime.subscription USING btree (subscription_id, entity, filters, action_filter, COALESCE(selected_columns, '{}'::text[]));


--
-- Name: bname; Type: INDEX; Schema: storage; Owner: supabase_storage_admin
--

CREATE UNIQUE INDEX bname ON storage.buckets USING btree (name);


--
-- Name: buckets_analytics_unique_name_idx; Type: INDEX; Schema: storage; Owner: supabase_storage_admin
--

CREATE UNIQUE INDEX buckets_analytics_unique_name_idx ON storage.buckets_analytics USING btree (name) WHERE (deleted_at IS NULL);


--
-- Name: idx_multipart_uploads_list; Type: INDEX; Schema: storage; Owner: supabase_storage_admin
--

CREATE INDEX idx_multipart_uploads_list ON storage.s3_multipart_uploads USING btree (bucket_id, key, created_at);


--
-- Name: idx_objects_bucket_id_name; Type: INDEX; Schema: storage; Owner: supabase_storage_admin
--

CREATE INDEX idx_objects_bucket_id_name ON storage.objects USING btree (bucket_id, name COLLATE "C");


--
-- Name: idx_objects_bucket_id_name_lower; Type: INDEX; Schema: storage; Owner: supabase_storage_admin
--

CREATE INDEX idx_objects_bucket_id_name_lower ON storage.objects USING btree (bucket_id, lower(name) COLLATE "C");


--
-- Name: idx_objects_current_version; Type: INDEX; Schema: storage; Owner: supabase_storage_admin
--

CREATE UNIQUE INDEX idx_objects_current_version ON storage.objects USING btree (bucket_id, name COLLATE "C") WHERE (archived_at IS NULL);


--
-- Name: idx_objects_delete_markers; Type: INDEX; Schema: storage; Owner: supabase_storage_admin
--

CREATE INDEX idx_objects_delete_markers ON storage.objects USING btree (bucket_id, name COLLATE "C") WHERE is_delete_marker;


--
-- Name: idx_objects_null_version; Type: INDEX; Schema: storage; Owner: supabase_storage_admin
--

CREATE UNIQUE INDEX idx_objects_null_version ON storage.objects USING btree (bucket_id, name COLLATE "C") WHERE (NOT is_versioned);


--
-- Name: name_prefix_search; Type: INDEX; Schema: storage; Owner: supabase_storage_admin
--

CREATE INDEX name_prefix_search ON storage.objects USING btree (name text_pattern_ops);


--
-- Name: objects_bucket_id_name_version_key; Type: INDEX; Schema: storage; Owner: supabase_storage_admin
--

CREATE UNIQUE INDEX objects_bucket_id_name_version_key ON storage.objects USING btree (bucket_id, name COLLATE "C", version) NULLS NOT DISTINCT;


--
-- Name: vector_indexes_name_bucket_id_idx; Type: INDEX; Schema: storage; Owner: supabase_storage_admin
--

CREATE UNIQUE INDEX vector_indexes_name_bucket_id_idx ON storage.vector_indexes USING btree (name, bucket_id);


--
-- Name: subscription tr_check_filters; Type: TRIGGER; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE TRIGGER tr_check_filters BEFORE INSERT OR UPDATE ON realtime.subscription FOR EACH ROW EXECUTE FUNCTION realtime.subscription_check_filters();


--
-- Name: buckets enforce_bucket_name_length_trigger; Type: TRIGGER; Schema: storage; Owner: supabase_storage_admin
--

CREATE TRIGGER enforce_bucket_name_length_trigger BEFORE INSERT OR UPDATE OF name ON storage.buckets FOR EACH ROW EXECUTE FUNCTION storage.enforce_bucket_name_length();


--
-- Name: buckets protect_bucket_control_insert; Type: TRIGGER; Schema: storage; Owner: supabase_storage_admin
--

CREATE TRIGGER protect_bucket_control_insert BEFORE INSERT ON storage.buckets FOR EACH ROW EXECUTE FUNCTION storage.protect_bucket_control_columns('service_role');


--
-- Name: buckets protect_bucket_control_update; Type: TRIGGER; Schema: storage; Owner: supabase_storage_admin
--

CREATE TRIGGER protect_bucket_control_update BEFORE UPDATE OF lifecycle_configuration, lifecycle_configuration_generation ON storage.buckets FOR EACH ROW EXECUTE FUNCTION storage.protect_bucket_control_columns();


--
-- Name: buckets protect_bucket_control_update_role; Type: TRIGGER; Schema: storage; Owner: supabase_storage_admin
--

CREATE TRIGGER protect_bucket_control_update_role AFTER UPDATE OF lifecycle_configuration, lifecycle_configuration_generation ON storage.buckets FOR EACH ROW EXECUTE FUNCTION storage.enforce_bucket_lifecycle_service_role('service_role');


--
-- Name: buckets protect_buckets_delete; Type: TRIGGER; Schema: storage; Owner: supabase_storage_admin
--

CREATE TRIGGER protect_buckets_delete BEFORE DELETE ON storage.buckets FOR EACH STATEMENT EXECUTE FUNCTION storage.protect_delete();


--
-- Name: objects protect_objects_delete; Type: TRIGGER; Schema: storage; Owner: supabase_storage_admin
--

CREATE TRIGGER protect_objects_delete BEFORE DELETE ON storage.objects FOR EACH STATEMENT EXECUTE FUNCTION storage.protect_delete();


--
-- Name: objects update_objects_updated_at; Type: TRIGGER; Schema: storage; Owner: supabase_storage_admin
--

CREATE TRIGGER update_objects_updated_at BEFORE UPDATE ON storage.objects FOR EACH ROW EXECUTE FUNCTION storage.update_updated_at_column();


--
-- Name: identities identities_user_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.identities
    ADD CONSTRAINT identities_user_id_fkey FOREIGN KEY (user_id) REFERENCES auth.users(id) ON DELETE CASCADE;


--
-- Name: mfa_amr_claims mfa_amr_claims_session_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_amr_claims
    ADD CONSTRAINT mfa_amr_claims_session_id_fkey FOREIGN KEY (session_id) REFERENCES auth.sessions(id) ON DELETE CASCADE;


--
-- Name: mfa_challenges mfa_challenges_auth_factor_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_challenges
    ADD CONSTRAINT mfa_challenges_auth_factor_id_fkey FOREIGN KEY (factor_id) REFERENCES auth.mfa_factors(id) ON DELETE CASCADE;


--
-- Name: mfa_factors mfa_factors_user_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_factors
    ADD CONSTRAINT mfa_factors_user_id_fkey FOREIGN KEY (user_id) REFERENCES auth.users(id) ON DELETE CASCADE;


--
-- Name: mfa_recovery_code_sets mfa_recovery_code_sets_mfa_factor_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_recovery_code_sets
    ADD CONSTRAINT mfa_recovery_code_sets_mfa_factor_id_fkey FOREIGN KEY (mfa_factor_id) REFERENCES auth.mfa_factors(id) ON DELETE CASCADE;


--
-- Name: mfa_recovery_code_sets mfa_recovery_code_sets_user_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_recovery_code_sets
    ADD CONSTRAINT mfa_recovery_code_sets_user_id_fkey FOREIGN KEY (user_id) REFERENCES auth.users(id) ON DELETE CASCADE;


--
-- Name: mfa_recovery_codes mfa_recovery_codes_mfa_recovery_code_set_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_recovery_codes
    ADD CONSTRAINT mfa_recovery_codes_mfa_recovery_code_set_id_fkey FOREIGN KEY (mfa_recovery_code_set_id) REFERENCES auth.mfa_recovery_code_sets(id) ON DELETE CASCADE;


--
-- Name: oauth_authorizations oauth_authorizations_client_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.oauth_authorizations
    ADD CONSTRAINT oauth_authorizations_client_id_fkey FOREIGN KEY (client_id) REFERENCES auth.oauth_clients(id) ON DELETE CASCADE;


--
-- Name: oauth_authorizations oauth_authorizations_user_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.oauth_authorizations
    ADD CONSTRAINT oauth_authorizations_user_id_fkey FOREIGN KEY (user_id) REFERENCES auth.users(id) ON DELETE CASCADE;


--
-- Name: oauth_consents oauth_consents_client_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.oauth_consents
    ADD CONSTRAINT oauth_consents_client_id_fkey FOREIGN KEY (client_id) REFERENCES auth.oauth_clients(id) ON DELETE CASCADE;


--
-- Name: oauth_consents oauth_consents_user_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.oauth_consents
    ADD CONSTRAINT oauth_consents_user_id_fkey FOREIGN KEY (user_id) REFERENCES auth.users(id) ON DELETE CASCADE;


--
-- Name: one_time_tokens one_time_tokens_user_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.one_time_tokens
    ADD CONSTRAINT one_time_tokens_user_id_fkey FOREIGN KEY (user_id) REFERENCES auth.users(id) ON DELETE CASCADE;


--
-- Name: refresh_tokens refresh_tokens_session_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.refresh_tokens
    ADD CONSTRAINT refresh_tokens_session_id_fkey FOREIGN KEY (session_id) REFERENCES auth.sessions(id) ON DELETE CASCADE;


--
-- Name: saml_providers saml_providers_sso_provider_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.saml_providers
    ADD CONSTRAINT saml_providers_sso_provider_id_fkey FOREIGN KEY (sso_provider_id) REFERENCES auth.sso_providers(id) ON DELETE CASCADE;


--
-- Name: saml_relay_states saml_relay_states_flow_state_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.saml_relay_states
    ADD CONSTRAINT saml_relay_states_flow_state_id_fkey FOREIGN KEY (flow_state_id) REFERENCES auth.flow_state(id) ON DELETE CASCADE;


--
-- Name: saml_relay_states saml_relay_states_sso_provider_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.saml_relay_states
    ADD CONSTRAINT saml_relay_states_sso_provider_id_fkey FOREIGN KEY (sso_provider_id) REFERENCES auth.sso_providers(id) ON DELETE CASCADE;


--
-- Name: scim_tokens scim_tokens_sso_provider_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.scim_tokens
    ADD CONSTRAINT scim_tokens_sso_provider_id_fkey FOREIGN KEY (sso_provider_id) REFERENCES auth.sso_providers(id) ON DELETE CASCADE;


--
-- Name: scim_users scim_users_sso_provider_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.scim_users
    ADD CONSTRAINT scim_users_sso_provider_id_fkey FOREIGN KEY (sso_provider_id) REFERENCES auth.sso_providers(id) ON DELETE CASCADE;


--
-- Name: scim_users scim_users_user_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.scim_users
    ADD CONSTRAINT scim_users_user_id_fkey FOREIGN KEY (user_id) REFERENCES auth.users(id) ON DELETE SET NULL;


--
-- Name: sessions sessions_oauth_client_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.sessions
    ADD CONSTRAINT sessions_oauth_client_id_fkey FOREIGN KEY (oauth_client_id) REFERENCES auth.oauth_clients(id) ON DELETE CASCADE;


--
-- Name: sessions sessions_user_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.sessions
    ADD CONSTRAINT sessions_user_id_fkey FOREIGN KEY (user_id) REFERENCES auth.users(id) ON DELETE CASCADE;


--
-- Name: sso_domains sso_domains_sso_provider_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.sso_domains
    ADD CONSTRAINT sso_domains_sso_provider_id_fkey FOREIGN KEY (sso_provider_id) REFERENCES auth.sso_providers(id) ON DELETE CASCADE;


--
-- Name: webauthn_challenges webauthn_challenges_user_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.webauthn_challenges
    ADD CONSTRAINT webauthn_challenges_user_id_fkey FOREIGN KEY (user_id) REFERENCES auth.users(id) ON DELETE CASCADE;


--
-- Name: webauthn_credentials webauthn_credentials_user_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.webauthn_credentials
    ADD CONSTRAINT webauthn_credentials_user_id_fkey FOREIGN KEY (user_id) REFERENCES auth.users(id) ON DELETE CASCADE;


--
-- Name: attendance_report_months attendance_report_months_school_year_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attendance_report_months
    ADD CONSTRAINT attendance_report_months_school_year_id_foreign FOREIGN KEY (school_year_id) REFERENCES public.school_years(id) ON DELETE CASCADE;


--
-- Name: attendance_report_sections attendance_report_sections_attendance_report_month_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attendance_report_sections
    ADD CONSTRAINT attendance_report_sections_attendance_report_month_id_foreign FOREIGN KEY (attendance_report_month_id) REFERENCES public.attendance_report_months(id) ON DELETE CASCADE;


--
-- Name: audit_logs audit_logs_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.audit_logs
    ADD CONSTRAINT audit_logs_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: enrollments enrollments_school_year_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.enrollments
    ADD CONSTRAINT enrollments_school_year_id_foreign FOREIGN KEY (school_year_id) REFERENCES public.school_years(id) ON DELETE CASCADE;


--
-- Name: enrollments enrollments_student_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.enrollments
    ADD CONSTRAINT enrollments_student_id_foreign FOREIGN KEY (student_id) REFERENCES public.students(id) ON DELETE CASCADE;


--
-- Name: meal_plans meal_plans_recorded_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.meal_plans
    ADD CONSTRAINT meal_plans_recorded_by_user_id_foreign FOREIGN KEY (recorded_by_user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: nutrition_measurements nutrition_measurements_sbfp_participant_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.nutrition_measurements
    ADD CONSTRAINT nutrition_measurements_sbfp_participant_id_foreign FOREIGN KEY (sbfp_participant_id) REFERENCES public.sbfp_participants(id) ON DELETE CASCADE;


--
-- Name: report_period_rows report_period_rows_report_period_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.report_period_rows
    ADD CONSTRAINT report_period_rows_report_period_id_foreign FOREIGN KEY (report_period_id) REFERENCES public.report_periods(id) ON DELETE CASCADE;


--
-- Name: report_periods report_periods_school_year_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.report_periods
    ADD CONSTRAINT report_periods_school_year_id_foreign FOREIGN KEY (school_year_id) REFERENCES public.school_years(id) ON DELETE CASCADE;


--
-- Name: sbfp_parent_approval_requests sbfp_parent_approval_requests_closed_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sbfp_parent_approval_requests
    ADD CONSTRAINT sbfp_parent_approval_requests_closed_by_user_id_foreign FOREIGN KEY (closed_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: sbfp_parent_approval_requests sbfp_parent_approval_requests_sbfp_participant_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sbfp_parent_approval_requests
    ADD CONSTRAINT sbfp_parent_approval_requests_sbfp_participant_id_foreign FOREIGN KEY (sbfp_participant_id) REFERENCES public.sbfp_participants(id) ON DELETE CASCADE;


--
-- Name: sbfp_participants sbfp_participants_enrollment_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sbfp_participants
    ADD CONSTRAINT sbfp_participants_enrollment_id_foreign FOREIGN KEY (enrollment_id) REFERENCES public.enrollments(id) ON DELETE CASCADE;


--
-- Name: school_year_user_records school_year_user_records_school_year_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.school_year_user_records
    ADD CONSTRAINT school_year_user_records_school_year_id_foreign FOREIGN KEY (school_year_id) REFERENCES public.school_years(id) ON DELETE CASCADE;


--
-- Name: school_year_user_records school_year_user_records_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.school_year_user_records
    ADD CONSTRAINT school_year_user_records_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: student_attendance_records student_attendance_records_recorded_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.student_attendance_records
    ADD CONSTRAINT student_attendance_records_recorded_by_user_id_foreign FOREIGN KEY (recorded_by_user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: student_attendance_records student_attendance_records_sbfp_participant_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.student_attendance_records
    ADD CONSTRAINT student_attendance_records_sbfp_participant_id_foreign FOREIGN KEY (sbfp_participant_id) REFERENCES public.sbfp_participants(id) ON DELETE CASCADE;


--
-- Name: student_feeding_records student_feeding_records_recorded_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.student_feeding_records
    ADD CONSTRAINT student_feeding_records_recorded_by_user_id_foreign FOREIGN KEY (recorded_by_user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: student_feeding_records student_feeding_records_sbfp_participant_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.student_feeding_records
    ADD CONSTRAINT student_feeding_records_sbfp_participant_id_foreign FOREIGN KEY (sbfp_participant_id) REFERENCES public.sbfp_participants(id) ON DELETE CASCADE;


--
-- Name: objects objects_bucketId_fkey; Type: FK CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.objects
    ADD CONSTRAINT "objects_bucketId_fkey" FOREIGN KEY (bucket_id) REFERENCES storage.buckets(id);


--
-- Name: s3_multipart_uploads s3_multipart_uploads_bucket_id_fkey; Type: FK CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.s3_multipart_uploads
    ADD CONSTRAINT s3_multipart_uploads_bucket_id_fkey FOREIGN KEY (bucket_id) REFERENCES storage.buckets(id);


--
-- Name: s3_multipart_uploads_parts s3_multipart_uploads_parts_bucket_id_fkey; Type: FK CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.s3_multipart_uploads_parts
    ADD CONSTRAINT s3_multipart_uploads_parts_bucket_id_fkey FOREIGN KEY (bucket_id) REFERENCES storage.buckets(id);


--
-- Name: s3_multipart_uploads_parts s3_multipart_uploads_parts_upload_id_fkey; Type: FK CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.s3_multipart_uploads_parts
    ADD CONSTRAINT s3_multipart_uploads_parts_upload_id_fkey FOREIGN KEY (upload_id) REFERENCES storage.s3_multipart_uploads(id) ON DELETE CASCADE;


--
-- Name: vector_indexes vector_indexes_bucket_id_fkey; Type: FK CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.vector_indexes
    ADD CONSTRAINT vector_indexes_bucket_id_fkey FOREIGN KEY (bucket_id) REFERENCES storage.buckets_vectors(id);


--
-- Name: audit_log_entries; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.audit_log_entries ENABLE ROW LEVEL SECURITY;

--
-- Name: flow_state; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.flow_state ENABLE ROW LEVEL SECURITY;

--
-- Name: identities; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.identities ENABLE ROW LEVEL SECURITY;

--
-- Name: instances; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.instances ENABLE ROW LEVEL SECURITY;

--
-- Name: mfa_amr_claims; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.mfa_amr_claims ENABLE ROW LEVEL SECURITY;

--
-- Name: mfa_challenges; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.mfa_challenges ENABLE ROW LEVEL SECURITY;

--
-- Name: mfa_factors; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.mfa_factors ENABLE ROW LEVEL SECURITY;

--
-- Name: one_time_tokens; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.one_time_tokens ENABLE ROW LEVEL SECURITY;

--
-- Name: refresh_tokens; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.refresh_tokens ENABLE ROW LEVEL SECURITY;

--
-- Name: saml_providers; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.saml_providers ENABLE ROW LEVEL SECURITY;

--
-- Name: saml_relay_states; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.saml_relay_states ENABLE ROW LEVEL SECURITY;

--
-- Name: schema_migrations; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.schema_migrations ENABLE ROW LEVEL SECURITY;

--
-- Name: sessions; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.sessions ENABLE ROW LEVEL SECURITY;

--
-- Name: sso_domains; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.sso_domains ENABLE ROW LEVEL SECURITY;

--
-- Name: sso_providers; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.sso_providers ENABLE ROW LEVEL SECURITY;

--
-- Name: users; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.users ENABLE ROW LEVEL SECURITY;

--
-- Name: messages; Type: ROW SECURITY; Schema: realtime; Owner: supabase_realtime_admin
--

ALTER TABLE realtime.messages ENABLE ROW LEVEL SECURITY;

--
-- Name: buckets; Type: ROW SECURITY; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE storage.buckets ENABLE ROW LEVEL SECURITY;

--
-- Name: buckets_analytics; Type: ROW SECURITY; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE storage.buckets_analytics ENABLE ROW LEVEL SECURITY;

--
-- Name: buckets_vectors; Type: ROW SECURITY; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE storage.buckets_vectors ENABLE ROW LEVEL SECURITY;

--
-- Name: migrations; Type: ROW SECURITY; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE storage.migrations ENABLE ROW LEVEL SECURITY;

--
-- Name: objects; Type: ROW SECURITY; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE storage.objects ENABLE ROW LEVEL SECURITY;

--
-- Name: s3_multipart_uploads; Type: ROW SECURITY; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE storage.s3_multipart_uploads ENABLE ROW LEVEL SECURITY;

--
-- Name: s3_multipart_uploads_parts; Type: ROW SECURITY; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE storage.s3_multipart_uploads_parts ENABLE ROW LEVEL SECURITY;

--
-- Name: vector_indexes; Type: ROW SECURITY; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE storage.vector_indexes ENABLE ROW LEVEL SECURITY;

--
-- Name: supabase_realtime; Type: PUBLICATION; Schema: -; Owner: postgres
--

CREATE PUBLICATION supabase_realtime WITH (publish = 'insert, update, delete, truncate');


ALTER PUBLICATION supabase_realtime OWNER TO postgres;

--
-- Name: SCHEMA auth; Type: ACL; Schema: -; Owner: supabase_admin
--

GRANT USAGE ON SCHEMA auth TO anon;
GRANT USAGE ON SCHEMA auth TO authenticated;
GRANT USAGE ON SCHEMA auth TO service_role;
GRANT ALL ON SCHEMA auth TO supabase_auth_admin;
GRANT ALL ON SCHEMA auth TO dashboard_user;
GRANT USAGE ON SCHEMA auth TO postgres;


--
-- Name: SCHEMA extensions; Type: ACL; Schema: -; Owner: postgres
--

GRANT USAGE ON SCHEMA extensions TO anon;
GRANT USAGE ON SCHEMA extensions TO authenticated;
GRANT USAGE ON SCHEMA extensions TO service_role;
GRANT ALL ON SCHEMA extensions TO dashboard_user;


--
-- Name: SCHEMA public; Type: ACL; Schema: -; Owner: pg_database_owner
--

GRANT USAGE ON SCHEMA public TO postgres;
GRANT USAGE ON SCHEMA public TO anon;
GRANT USAGE ON SCHEMA public TO authenticated;
GRANT USAGE ON SCHEMA public TO service_role;


--
-- Name: SCHEMA realtime; Type: ACL; Schema: -; Owner: supabase_admin
--

GRANT USAGE ON SCHEMA realtime TO postgres WITH GRANT OPTION;
GRANT USAGE ON SCHEMA realtime TO anon;
GRANT USAGE ON SCHEMA realtime TO authenticated;
GRANT USAGE ON SCHEMA realtime TO service_role;
GRANT ALL ON SCHEMA realtime TO supabase_realtime_admin;


--
-- Name: SCHEMA storage; Type: ACL; Schema: -; Owner: supabase_admin
--

GRANT USAGE ON SCHEMA storage TO postgres WITH GRANT OPTION;
GRANT USAGE ON SCHEMA storage TO anon;
GRANT USAGE ON SCHEMA storage TO authenticated;
GRANT USAGE ON SCHEMA storage TO service_role;
GRANT ALL ON SCHEMA storage TO supabase_storage_admin WITH GRANT OPTION;
GRANT ALL ON SCHEMA storage TO dashboard_user;


--
-- Name: SCHEMA vault; Type: ACL; Schema: -; Owner: supabase_admin
--

GRANT USAGE ON SCHEMA vault TO postgres WITH GRANT OPTION;
GRANT USAGE ON SCHEMA vault TO service_role;


--
-- Name: FUNCTION email(); Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON FUNCTION auth.email() TO dashboard_user;


--
-- Name: FUNCTION jwt(); Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON FUNCTION auth.jwt() TO postgres;
GRANT ALL ON FUNCTION auth.jwt() TO dashboard_user;


--
-- Name: FUNCTION role(); Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON FUNCTION auth.role() TO dashboard_user;


--
-- Name: FUNCTION uid(); Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON FUNCTION auth.uid() TO dashboard_user;


--
-- Name: FUNCTION armor(bytea); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.armor(bytea) FROM postgres;
GRANT ALL ON FUNCTION extensions.armor(bytea) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.armor(bytea) TO dashboard_user;


--
-- Name: FUNCTION armor(bytea, text[], text[]); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.armor(bytea, text[], text[]) FROM postgres;
GRANT ALL ON FUNCTION extensions.armor(bytea, text[], text[]) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.armor(bytea, text[], text[]) TO dashboard_user;


--
-- Name: FUNCTION crypt(text, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.crypt(text, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.crypt(text, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.crypt(text, text) TO dashboard_user;


--
-- Name: FUNCTION dearmor(text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.dearmor(text) FROM postgres;
GRANT ALL ON FUNCTION extensions.dearmor(text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.dearmor(text) TO dashboard_user;


--
-- Name: FUNCTION decrypt(bytea, bytea, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.decrypt(bytea, bytea, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.decrypt(bytea, bytea, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.decrypt(bytea, bytea, text) TO dashboard_user;


--
-- Name: FUNCTION decrypt_iv(bytea, bytea, bytea, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.decrypt_iv(bytea, bytea, bytea, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.decrypt_iv(bytea, bytea, bytea, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.decrypt_iv(bytea, bytea, bytea, text) TO dashboard_user;


--
-- Name: FUNCTION digest(bytea, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.digest(bytea, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.digest(bytea, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.digest(bytea, text) TO dashboard_user;


--
-- Name: FUNCTION digest(text, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.digest(text, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.digest(text, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.digest(text, text) TO dashboard_user;


--
-- Name: FUNCTION encrypt(bytea, bytea, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.encrypt(bytea, bytea, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.encrypt(bytea, bytea, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.encrypt(bytea, bytea, text) TO dashboard_user;


--
-- Name: FUNCTION encrypt_iv(bytea, bytea, bytea, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.encrypt_iv(bytea, bytea, bytea, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.encrypt_iv(bytea, bytea, bytea, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.encrypt_iv(bytea, bytea, bytea, text) TO dashboard_user;


--
-- Name: FUNCTION gen_random_bytes(integer); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.gen_random_bytes(integer) FROM postgres;
GRANT ALL ON FUNCTION extensions.gen_random_bytes(integer) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.gen_random_bytes(integer) TO dashboard_user;


--
-- Name: FUNCTION gen_random_uuid(); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.gen_random_uuid() FROM postgres;
GRANT ALL ON FUNCTION extensions.gen_random_uuid() TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.gen_random_uuid() TO dashboard_user;


--
-- Name: FUNCTION gen_salt(text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.gen_salt(text) FROM postgres;
GRANT ALL ON FUNCTION extensions.gen_salt(text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.gen_salt(text) TO dashboard_user;


--
-- Name: FUNCTION gen_salt(text, integer); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.gen_salt(text, integer) FROM postgres;
GRANT ALL ON FUNCTION extensions.gen_salt(text, integer) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.gen_salt(text, integer) TO dashboard_user;


--
-- Name: FUNCTION grant_pg_cron_access(); Type: ACL; Schema: extensions; Owner: supabase_admin
--

REVOKE ALL ON FUNCTION extensions.grant_pg_cron_access() FROM supabase_admin;
GRANT ALL ON FUNCTION extensions.grant_pg_cron_access() TO supabase_admin WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.grant_pg_cron_access() TO dashboard_user;


--
-- Name: FUNCTION grant_pg_graphql_access(); Type: ACL; Schema: extensions; Owner: supabase_admin
--

GRANT ALL ON FUNCTION extensions.grant_pg_graphql_access() TO postgres WITH GRANT OPTION;


--
-- Name: FUNCTION grant_pg_net_access(); Type: ACL; Schema: extensions; Owner: supabase_admin
--

REVOKE ALL ON FUNCTION extensions.grant_pg_net_access() FROM supabase_admin;
GRANT ALL ON FUNCTION extensions.grant_pg_net_access() TO supabase_admin WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.grant_pg_net_access() TO dashboard_user;


--
-- Name: FUNCTION hmac(bytea, bytea, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.hmac(bytea, bytea, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.hmac(bytea, bytea, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.hmac(bytea, bytea, text) TO dashboard_user;


--
-- Name: FUNCTION hmac(text, text, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.hmac(text, text, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.hmac(text, text, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.hmac(text, text, text) TO dashboard_user;


--
-- Name: FUNCTION pg_stat_statements(showtext boolean, OUT userid oid, OUT dbid oid, OUT toplevel boolean, OUT queryid bigint, OUT query text, OUT plans bigint, OUT total_plan_time double precision, OUT min_plan_time double precision, OUT max_plan_time double precision, OUT mean_plan_time double precision, OUT stddev_plan_time double precision, OUT calls bigint, OUT total_exec_time double precision, OUT min_exec_time double precision, OUT max_exec_time double precision, OUT mean_exec_time double precision, OUT stddev_exec_time double precision, OUT rows bigint, OUT shared_blks_hit bigint, OUT shared_blks_read bigint, OUT shared_blks_dirtied bigint, OUT shared_blks_written bigint, OUT local_blks_hit bigint, OUT local_blks_read bigint, OUT local_blks_dirtied bigint, OUT local_blks_written bigint, OUT temp_blks_read bigint, OUT temp_blks_written bigint, OUT shared_blk_read_time double precision, OUT shared_blk_write_time double precision, OUT local_blk_read_time double precision, OUT local_blk_write_time double precision, OUT temp_blk_read_time double precision, OUT temp_blk_write_time double precision, OUT wal_records bigint, OUT wal_fpi bigint, OUT wal_bytes numeric, OUT jit_functions bigint, OUT jit_generation_time double precision, OUT jit_inlining_count bigint, OUT jit_inlining_time double precision, OUT jit_optimization_count bigint, OUT jit_optimization_time double precision, OUT jit_emission_count bigint, OUT jit_emission_time double precision, OUT jit_deform_count bigint, OUT jit_deform_time double precision, OUT stats_since timestamp with time zone, OUT minmax_stats_since timestamp with time zone); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pg_stat_statements(showtext boolean, OUT userid oid, OUT dbid oid, OUT toplevel boolean, OUT queryid bigint, OUT query text, OUT plans bigint, OUT total_plan_time double precision, OUT min_plan_time double precision, OUT max_plan_time double precision, OUT mean_plan_time double precision, OUT stddev_plan_time double precision, OUT calls bigint, OUT total_exec_time double precision, OUT min_exec_time double precision, OUT max_exec_time double precision, OUT mean_exec_time double precision, OUT stddev_exec_time double precision, OUT rows bigint, OUT shared_blks_hit bigint, OUT shared_blks_read bigint, OUT shared_blks_dirtied bigint, OUT shared_blks_written bigint, OUT local_blks_hit bigint, OUT local_blks_read bigint, OUT local_blks_dirtied bigint, OUT local_blks_written bigint, OUT temp_blks_read bigint, OUT temp_blks_written bigint, OUT shared_blk_read_time double precision, OUT shared_blk_write_time double precision, OUT local_blk_read_time double precision, OUT local_blk_write_time double precision, OUT temp_blk_read_time double precision, OUT temp_blk_write_time double precision, OUT wal_records bigint, OUT wal_fpi bigint, OUT wal_bytes numeric, OUT jit_functions bigint, OUT jit_generation_time double precision, OUT jit_inlining_count bigint, OUT jit_inlining_time double precision, OUT jit_optimization_count bigint, OUT jit_optimization_time double precision, OUT jit_emission_count bigint, OUT jit_emission_time double precision, OUT jit_deform_count bigint, OUT jit_deform_time double precision, OUT stats_since timestamp with time zone, OUT minmax_stats_since timestamp with time zone) FROM postgres;
GRANT ALL ON FUNCTION extensions.pg_stat_statements(showtext boolean, OUT userid oid, OUT dbid oid, OUT toplevel boolean, OUT queryid bigint, OUT query text, OUT plans bigint, OUT total_plan_time double precision, OUT min_plan_time double precision, OUT max_plan_time double precision, OUT mean_plan_time double precision, OUT stddev_plan_time double precision, OUT calls bigint, OUT total_exec_time double precision, OUT min_exec_time double precision, OUT max_exec_time double precision, OUT mean_exec_time double precision, OUT stddev_exec_time double precision, OUT rows bigint, OUT shared_blks_hit bigint, OUT shared_blks_read bigint, OUT shared_blks_dirtied bigint, OUT shared_blks_written bigint, OUT local_blks_hit bigint, OUT local_blks_read bigint, OUT local_blks_dirtied bigint, OUT local_blks_written bigint, OUT temp_blks_read bigint, OUT temp_blks_written bigint, OUT shared_blk_read_time double precision, OUT shared_blk_write_time double precision, OUT local_blk_read_time double precision, OUT local_blk_write_time double precision, OUT temp_blk_read_time double precision, OUT temp_blk_write_time double precision, OUT wal_records bigint, OUT wal_fpi bigint, OUT wal_bytes numeric, OUT jit_functions bigint, OUT jit_generation_time double precision, OUT jit_inlining_count bigint, OUT jit_inlining_time double precision, OUT jit_optimization_count bigint, OUT jit_optimization_time double precision, OUT jit_emission_count bigint, OUT jit_emission_time double precision, OUT jit_deform_count bigint, OUT jit_deform_time double precision, OUT stats_since timestamp with time zone, OUT minmax_stats_since timestamp with time zone) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pg_stat_statements(showtext boolean, OUT userid oid, OUT dbid oid, OUT toplevel boolean, OUT queryid bigint, OUT query text, OUT plans bigint, OUT total_plan_time double precision, OUT min_plan_time double precision, OUT max_plan_time double precision, OUT mean_plan_time double precision, OUT stddev_plan_time double precision, OUT calls bigint, OUT total_exec_time double precision, OUT min_exec_time double precision, OUT max_exec_time double precision, OUT mean_exec_time double precision, OUT stddev_exec_time double precision, OUT rows bigint, OUT shared_blks_hit bigint, OUT shared_blks_read bigint, OUT shared_blks_dirtied bigint, OUT shared_blks_written bigint, OUT local_blks_hit bigint, OUT local_blks_read bigint, OUT local_blks_dirtied bigint, OUT local_blks_written bigint, OUT temp_blks_read bigint, OUT temp_blks_written bigint, OUT shared_blk_read_time double precision, OUT shared_blk_write_time double precision, OUT local_blk_read_time double precision, OUT local_blk_write_time double precision, OUT temp_blk_read_time double precision, OUT temp_blk_write_time double precision, OUT wal_records bigint, OUT wal_fpi bigint, OUT wal_bytes numeric, OUT jit_functions bigint, OUT jit_generation_time double precision, OUT jit_inlining_count bigint, OUT jit_inlining_time double precision, OUT jit_optimization_count bigint, OUT jit_optimization_time double precision, OUT jit_emission_count bigint, OUT jit_emission_time double precision, OUT jit_deform_count bigint, OUT jit_deform_time double precision, OUT stats_since timestamp with time zone, OUT minmax_stats_since timestamp with time zone) TO dashboard_user;


--
-- Name: FUNCTION pg_stat_statements_info(OUT dealloc bigint, OUT stats_reset timestamp with time zone); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pg_stat_statements_info(OUT dealloc bigint, OUT stats_reset timestamp with time zone) FROM postgres;
GRANT ALL ON FUNCTION extensions.pg_stat_statements_info(OUT dealloc bigint, OUT stats_reset timestamp with time zone) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pg_stat_statements_info(OUT dealloc bigint, OUT stats_reset timestamp with time zone) TO dashboard_user;


--
-- Name: FUNCTION pg_stat_statements_reset(userid oid, dbid oid, queryid bigint, minmax_only boolean); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pg_stat_statements_reset(userid oid, dbid oid, queryid bigint, minmax_only boolean) FROM postgres;
GRANT ALL ON FUNCTION extensions.pg_stat_statements_reset(userid oid, dbid oid, queryid bigint, minmax_only boolean) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pg_stat_statements_reset(userid oid, dbid oid, queryid bigint, minmax_only boolean) TO dashboard_user;


--
-- Name: FUNCTION pgp_armor_headers(text, OUT key text, OUT value text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_armor_headers(text, OUT key text, OUT value text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_armor_headers(text, OUT key text, OUT value text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_armor_headers(text, OUT key text, OUT value text) TO dashboard_user;


--
-- Name: FUNCTION pgp_key_id(bytea); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_key_id(bytea) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_key_id(bytea) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_key_id(bytea) TO dashboard_user;


--
-- Name: FUNCTION pgp_pub_decrypt(bytea, bytea); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_pub_decrypt(bytea, bytea) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_pub_decrypt(bytea, bytea) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_pub_decrypt(bytea, bytea) TO dashboard_user;


--
-- Name: FUNCTION pgp_pub_decrypt(bytea, bytea, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_pub_decrypt(bytea, bytea, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_pub_decrypt(bytea, bytea, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_pub_decrypt(bytea, bytea, text) TO dashboard_user;


--
-- Name: FUNCTION pgp_pub_decrypt(bytea, bytea, text, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_pub_decrypt(bytea, bytea, text, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_pub_decrypt(bytea, bytea, text, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_pub_decrypt(bytea, bytea, text, text) TO dashboard_user;


--
-- Name: FUNCTION pgp_pub_decrypt_bytea(bytea, bytea); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_pub_decrypt_bytea(bytea, bytea) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_pub_decrypt_bytea(bytea, bytea) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_pub_decrypt_bytea(bytea, bytea) TO dashboard_user;


--
-- Name: FUNCTION pgp_pub_decrypt_bytea(bytea, bytea, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_pub_decrypt_bytea(bytea, bytea, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_pub_decrypt_bytea(bytea, bytea, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_pub_decrypt_bytea(bytea, bytea, text) TO dashboard_user;


--
-- Name: FUNCTION pgp_pub_decrypt_bytea(bytea, bytea, text, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_pub_decrypt_bytea(bytea, bytea, text, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_pub_decrypt_bytea(bytea, bytea, text, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_pub_decrypt_bytea(bytea, bytea, text, text) TO dashboard_user;


--
-- Name: FUNCTION pgp_pub_encrypt(text, bytea); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_pub_encrypt(text, bytea) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_pub_encrypt(text, bytea) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_pub_encrypt(text, bytea) TO dashboard_user;


--
-- Name: FUNCTION pgp_pub_encrypt(text, bytea, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_pub_encrypt(text, bytea, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_pub_encrypt(text, bytea, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_pub_encrypt(text, bytea, text) TO dashboard_user;


--
-- Name: FUNCTION pgp_pub_encrypt_bytea(bytea, bytea); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_pub_encrypt_bytea(bytea, bytea) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_pub_encrypt_bytea(bytea, bytea) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_pub_encrypt_bytea(bytea, bytea) TO dashboard_user;


--
-- Name: FUNCTION pgp_pub_encrypt_bytea(bytea, bytea, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_pub_encrypt_bytea(bytea, bytea, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_pub_encrypt_bytea(bytea, bytea, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_pub_encrypt_bytea(bytea, bytea, text) TO dashboard_user;


--
-- Name: FUNCTION pgp_sym_decrypt(bytea, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_sym_decrypt(bytea, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_sym_decrypt(bytea, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_sym_decrypt(bytea, text) TO dashboard_user;


--
-- Name: FUNCTION pgp_sym_decrypt(bytea, text, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_sym_decrypt(bytea, text, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_sym_decrypt(bytea, text, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_sym_decrypt(bytea, text, text) TO dashboard_user;


--
-- Name: FUNCTION pgp_sym_decrypt_bytea(bytea, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_sym_decrypt_bytea(bytea, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_sym_decrypt_bytea(bytea, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_sym_decrypt_bytea(bytea, text) TO dashboard_user;


--
-- Name: FUNCTION pgp_sym_decrypt_bytea(bytea, text, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_sym_decrypt_bytea(bytea, text, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_sym_decrypt_bytea(bytea, text, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_sym_decrypt_bytea(bytea, text, text) TO dashboard_user;


--
-- Name: FUNCTION pgp_sym_encrypt(text, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_sym_encrypt(text, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_sym_encrypt(text, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_sym_encrypt(text, text) TO dashboard_user;


--
-- Name: FUNCTION pgp_sym_encrypt(text, text, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_sym_encrypt(text, text, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_sym_encrypt(text, text, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_sym_encrypt(text, text, text) TO dashboard_user;


--
-- Name: FUNCTION pgp_sym_encrypt_bytea(bytea, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_sym_encrypt_bytea(bytea, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_sym_encrypt_bytea(bytea, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_sym_encrypt_bytea(bytea, text) TO dashboard_user;


--
-- Name: FUNCTION pgp_sym_encrypt_bytea(bytea, text, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_sym_encrypt_bytea(bytea, text, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_sym_encrypt_bytea(bytea, text, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_sym_encrypt_bytea(bytea, text, text) TO dashboard_user;


--
-- Name: FUNCTION pgrst_ddl_watch(); Type: ACL; Schema: extensions; Owner: supabase_admin
--

GRANT ALL ON FUNCTION extensions.pgrst_ddl_watch() TO postgres WITH GRANT OPTION;


--
-- Name: FUNCTION pgrst_drop_watch(); Type: ACL; Schema: extensions; Owner: supabase_admin
--

GRANT ALL ON FUNCTION extensions.pgrst_drop_watch() TO postgres WITH GRANT OPTION;


--
-- Name: FUNCTION set_graphql_placeholder(); Type: ACL; Schema: extensions; Owner: supabase_admin
--

GRANT ALL ON FUNCTION extensions.set_graphql_placeholder() TO postgres WITH GRANT OPTION;


--
-- Name: FUNCTION uuid_generate_v1(); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.uuid_generate_v1() FROM postgres;
GRANT ALL ON FUNCTION extensions.uuid_generate_v1() TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.uuid_generate_v1() TO dashboard_user;


--
-- Name: FUNCTION uuid_generate_v1mc(); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.uuid_generate_v1mc() FROM postgres;
GRANT ALL ON FUNCTION extensions.uuid_generate_v1mc() TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.uuid_generate_v1mc() TO dashboard_user;


--
-- Name: FUNCTION uuid_generate_v3(namespace uuid, name text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.uuid_generate_v3(namespace uuid, name text) FROM postgres;
GRANT ALL ON FUNCTION extensions.uuid_generate_v3(namespace uuid, name text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.uuid_generate_v3(namespace uuid, name text) TO dashboard_user;


--
-- Name: FUNCTION uuid_generate_v4(); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.uuid_generate_v4() FROM postgres;
GRANT ALL ON FUNCTION extensions.uuid_generate_v4() TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.uuid_generate_v4() TO dashboard_user;


--
-- Name: FUNCTION uuid_generate_v5(namespace uuid, name text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.uuid_generate_v5(namespace uuid, name text) FROM postgres;
GRANT ALL ON FUNCTION extensions.uuid_generate_v5(namespace uuid, name text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.uuid_generate_v5(namespace uuid, name text) TO dashboard_user;


--
-- Name: FUNCTION uuid_nil(); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.uuid_nil() FROM postgres;
GRANT ALL ON FUNCTION extensions.uuid_nil() TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.uuid_nil() TO dashboard_user;


--
-- Name: FUNCTION uuid_ns_dns(); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.uuid_ns_dns() FROM postgres;
GRANT ALL ON FUNCTION extensions.uuid_ns_dns() TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.uuid_ns_dns() TO dashboard_user;


--
-- Name: FUNCTION uuid_ns_oid(); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.uuid_ns_oid() FROM postgres;
GRANT ALL ON FUNCTION extensions.uuid_ns_oid() TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.uuid_ns_oid() TO dashboard_user;


--
-- Name: FUNCTION uuid_ns_url(); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.uuid_ns_url() FROM postgres;
GRANT ALL ON FUNCTION extensions.uuid_ns_url() TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.uuid_ns_url() TO dashboard_user;


--
-- Name: FUNCTION uuid_ns_x500(); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.uuid_ns_x500() FROM postgres;
GRANT ALL ON FUNCTION extensions.uuid_ns_x500() TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.uuid_ns_x500() TO dashboard_user;


--
-- Name: FUNCTION graphql("operationName" text, query text, variables jsonb, extensions jsonb); Type: ACL; Schema: graphql_public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION graphql_public.graphql("operationName" text, query text, variables jsonb, extensions jsonb) TO postgres;
GRANT ALL ON FUNCTION graphql_public.graphql("operationName" text, query text, variables jsonb, extensions jsonb) TO anon;
GRANT ALL ON FUNCTION graphql_public.graphql("operationName" text, query text, variables jsonb, extensions jsonb) TO authenticated;
GRANT ALL ON FUNCTION graphql_public.graphql("operationName" text, query text, variables jsonb, extensions jsonb) TO service_role;


--
-- Name: FUNCTION pg_reload_conf(); Type: ACL; Schema: pg_catalog; Owner: supabase_admin
--

GRANT ALL ON FUNCTION pg_catalog.pg_reload_conf() TO postgres WITH GRANT OPTION;


--
-- Name: FUNCTION get_auth(p_usename text); Type: ACL; Schema: pgbouncer; Owner: supabase_admin
--

REVOKE ALL ON FUNCTION pgbouncer.get_auth(p_usename text) FROM PUBLIC;
GRANT ALL ON FUNCTION pgbouncer.get_auth(p_usename text) TO pgbouncer;


--
-- Name: FUNCTION apply_rls(wal jsonb, max_record_bytes integer); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.apply_rls(wal jsonb, max_record_bytes integer) TO postgres;
GRANT ALL ON FUNCTION realtime.apply_rls(wal jsonb, max_record_bytes integer) TO dashboard_user;
GRANT ALL ON FUNCTION realtime.apply_rls(wal jsonb, max_record_bytes integer) TO anon;
GRANT ALL ON FUNCTION realtime.apply_rls(wal jsonb, max_record_bytes integer) TO authenticated;
GRANT ALL ON FUNCTION realtime.apply_rls(wal jsonb, max_record_bytes integer) TO service_role;


--
-- Name: FUNCTION broadcast_changes(topic_name text, event_name text, operation text, table_name text, table_schema text, new record, old record, level text); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.broadcast_changes(topic_name text, event_name text, operation text, table_name text, table_schema text, new record, old record, level text) TO postgres;
GRANT ALL ON FUNCTION realtime.broadcast_changes(topic_name text, event_name text, operation text, table_name text, table_schema text, new record, old record, level text) TO dashboard_user;


--
-- Name: FUNCTION build_prepared_statement_sql(prepared_statement_name text, entity regclass, columns realtime.wal_column[]); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.build_prepared_statement_sql(prepared_statement_name text, entity regclass, columns realtime.wal_column[]) TO postgres;
GRANT ALL ON FUNCTION realtime.build_prepared_statement_sql(prepared_statement_name text, entity regclass, columns realtime.wal_column[]) TO dashboard_user;
GRANT ALL ON FUNCTION realtime.build_prepared_statement_sql(prepared_statement_name text, entity regclass, columns realtime.wal_column[]) TO anon;
GRANT ALL ON FUNCTION realtime.build_prepared_statement_sql(prepared_statement_name text, entity regclass, columns realtime.wal_column[]) TO authenticated;
GRANT ALL ON FUNCTION realtime.build_prepared_statement_sql(prepared_statement_name text, entity regclass, columns realtime.wal_column[]) TO service_role;


--
-- Name: FUNCTION "cast"(val text, type_ regtype); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime."cast"(val text, type_ regtype) TO postgres;
GRANT ALL ON FUNCTION realtime."cast"(val text, type_ regtype) TO dashboard_user;
GRANT ALL ON FUNCTION realtime."cast"(val text, type_ regtype) TO anon;
GRANT ALL ON FUNCTION realtime."cast"(val text, type_ regtype) TO authenticated;
GRANT ALL ON FUNCTION realtime."cast"(val text, type_ regtype) TO service_role;


--
-- Name: FUNCTION check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text) TO postgres;
GRANT ALL ON FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text) TO dashboard_user;
GRANT ALL ON FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text) TO anon;
GRANT ALL ON FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text) TO authenticated;
GRANT ALL ON FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text) TO service_role;


--
-- Name: FUNCTION check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text, negate boolean); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text, negate boolean) TO postgres;
GRANT ALL ON FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text, negate boolean) TO dashboard_user;
GRANT ALL ON FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text, negate boolean) TO anon;
GRANT ALL ON FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text, negate boolean) TO authenticated;
GRANT ALL ON FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text, negate boolean) TO service_role;


--
-- Name: FUNCTION is_visible_through_filters(columns realtime.wal_column[], filters realtime.user_defined_filter[]); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.is_visible_through_filters(columns realtime.wal_column[], filters realtime.user_defined_filter[]) TO postgres;
GRANT ALL ON FUNCTION realtime.is_visible_through_filters(columns realtime.wal_column[], filters realtime.user_defined_filter[]) TO dashboard_user;
GRANT ALL ON FUNCTION realtime.is_visible_through_filters(columns realtime.wal_column[], filters realtime.user_defined_filter[]) TO anon;
GRANT ALL ON FUNCTION realtime.is_visible_through_filters(columns realtime.wal_column[], filters realtime.user_defined_filter[]) TO authenticated;
GRANT ALL ON FUNCTION realtime.is_visible_through_filters(columns realtime.wal_column[], filters realtime.user_defined_filter[]) TO service_role;


--
-- Name: FUNCTION list_changes(publication name, slot_name name, max_changes integer, max_record_bytes integer); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.list_changes(publication name, slot_name name, max_changes integer, max_record_bytes integer) TO postgres;
GRANT ALL ON FUNCTION realtime.list_changes(publication name, slot_name name, max_changes integer, max_record_bytes integer) TO dashboard_user;


--
-- Name: FUNCTION quote_wal2json(entity regclass); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.quote_wal2json(entity regclass) TO postgres;
GRANT ALL ON FUNCTION realtime.quote_wal2json(entity regclass) TO dashboard_user;
GRANT ALL ON FUNCTION realtime.quote_wal2json(entity regclass) TO anon;
GRANT ALL ON FUNCTION realtime.quote_wal2json(entity regclass) TO authenticated;
GRANT ALL ON FUNCTION realtime.quote_wal2json(entity regclass) TO service_role;


--
-- Name: FUNCTION send(payload jsonb, event text, topic text, private boolean); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.send(payload jsonb, event text, topic text, private boolean) TO postgres;
GRANT ALL ON FUNCTION realtime.send(payload jsonb, event text, topic text, private boolean) TO dashboard_user;


--
-- Name: FUNCTION send_binary(payload bytea, event text, topic text, private boolean); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.send_binary(payload bytea, event text, topic text, private boolean) TO postgres;
GRANT ALL ON FUNCTION realtime.send_binary(payload bytea, event text, topic text, private boolean) TO dashboard_user;


--
-- Name: FUNCTION subscription_check_filters(); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.subscription_check_filters() TO postgres;
GRANT ALL ON FUNCTION realtime.subscription_check_filters() TO dashboard_user;
GRANT ALL ON FUNCTION realtime.subscription_check_filters() TO anon;
GRANT ALL ON FUNCTION realtime.subscription_check_filters() TO authenticated;
GRANT ALL ON FUNCTION realtime.subscription_check_filters() TO service_role;


--
-- Name: FUNCTION to_regrole(role_name text); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.to_regrole(role_name text) TO postgres;
GRANT ALL ON FUNCTION realtime.to_regrole(role_name text) TO dashboard_user;
GRANT ALL ON FUNCTION realtime.to_regrole(role_name text) TO anon;
GRANT ALL ON FUNCTION realtime.to_regrole(role_name text) TO authenticated;
GRANT ALL ON FUNCTION realtime.to_regrole(role_name text) TO service_role;


--
-- Name: FUNCTION topic(); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.topic() TO postgres;
GRANT ALL ON FUNCTION realtime.topic() TO dashboard_user;


--
-- Name: FUNCTION wal2json_escape_identifier(name text); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.wal2json_escape_identifier(name text) TO postgres;
GRANT ALL ON FUNCTION realtime.wal2json_escape_identifier(name text) TO dashboard_user;


--
-- Name: FUNCTION _crypto_aead_det_decrypt(message bytea, additional bytea, key_id bigint, context bytea, nonce bytea); Type: ACL; Schema: vault; Owner: supabase_admin
--

GRANT ALL ON FUNCTION vault._crypto_aead_det_decrypt(message bytea, additional bytea, key_id bigint, context bytea, nonce bytea) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION vault._crypto_aead_det_decrypt(message bytea, additional bytea, key_id bigint, context bytea, nonce bytea) TO service_role;


--
-- Name: FUNCTION create_secret(new_secret text, new_name text, new_description text, new_key_id uuid); Type: ACL; Schema: vault; Owner: supabase_admin
--

GRANT ALL ON FUNCTION vault.create_secret(new_secret text, new_name text, new_description text, new_key_id uuid) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION vault.create_secret(new_secret text, new_name text, new_description text, new_key_id uuid) TO service_role;


--
-- Name: FUNCTION update_secret(secret_id uuid, new_secret text, new_name text, new_description text, new_key_id uuid); Type: ACL; Schema: vault; Owner: supabase_admin
--

GRANT ALL ON FUNCTION vault.update_secret(secret_id uuid, new_secret text, new_name text, new_description text, new_key_id uuid) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION vault.update_secret(secret_id uuid, new_secret text, new_name text, new_description text, new_key_id uuid) TO service_role;


--
-- Name: TABLE audit_log_entries; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.audit_log_entries TO dashboard_user;
GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.audit_log_entries TO postgres;
GRANT SELECT ON TABLE auth.audit_log_entries TO postgres WITH GRANT OPTION;


--
-- Name: TABLE custom_oauth_providers; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.custom_oauth_providers TO postgres;
GRANT ALL ON TABLE auth.custom_oauth_providers TO dashboard_user;


--
-- Name: TABLE flow_state; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.flow_state TO postgres;
GRANT SELECT ON TABLE auth.flow_state TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE auth.flow_state TO dashboard_user;


--
-- Name: TABLE identities; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.identities TO postgres;
GRANT SELECT ON TABLE auth.identities TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE auth.identities TO dashboard_user;


--
-- Name: TABLE instances; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.instances TO dashboard_user;
GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.instances TO postgres;
GRANT SELECT ON TABLE auth.instances TO postgres WITH GRANT OPTION;


--
-- Name: TABLE mfa_amr_claims; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.mfa_amr_claims TO postgres;
GRANT SELECT ON TABLE auth.mfa_amr_claims TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE auth.mfa_amr_claims TO dashboard_user;


--
-- Name: TABLE mfa_challenges; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.mfa_challenges TO postgres;
GRANT SELECT ON TABLE auth.mfa_challenges TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE auth.mfa_challenges TO dashboard_user;


--
-- Name: TABLE mfa_factors; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.mfa_factors TO postgres;
GRANT SELECT ON TABLE auth.mfa_factors TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE auth.mfa_factors TO dashboard_user;


--
-- Name: TABLE mfa_recovery_code_sets; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.mfa_recovery_code_sets TO postgres;
GRANT ALL ON TABLE auth.mfa_recovery_code_sets TO dashboard_user;


--
-- Name: TABLE mfa_recovery_codes; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.mfa_recovery_codes TO postgres;
GRANT ALL ON TABLE auth.mfa_recovery_codes TO dashboard_user;


--
-- Name: TABLE oauth_authorizations; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.oauth_authorizations TO postgres;
GRANT ALL ON TABLE auth.oauth_authorizations TO dashboard_user;


--
-- Name: TABLE oauth_client_states; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.oauth_client_states TO postgres;
GRANT ALL ON TABLE auth.oauth_client_states TO dashboard_user;


--
-- Name: TABLE oauth_clients; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.oauth_clients TO postgres;
GRANT ALL ON TABLE auth.oauth_clients TO dashboard_user;


--
-- Name: TABLE oauth_consents; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.oauth_consents TO postgres;
GRANT ALL ON TABLE auth.oauth_consents TO dashboard_user;


--
-- Name: TABLE one_time_tokens; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.one_time_tokens TO postgres;
GRANT SELECT ON TABLE auth.one_time_tokens TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE auth.one_time_tokens TO dashboard_user;


--
-- Name: TABLE refresh_tokens; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.refresh_tokens TO dashboard_user;
GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.refresh_tokens TO postgres;
GRANT SELECT ON TABLE auth.refresh_tokens TO postgres WITH GRANT OPTION;


--
-- Name: SEQUENCE refresh_tokens_id_seq; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON SEQUENCE auth.refresh_tokens_id_seq TO dashboard_user;
GRANT ALL ON SEQUENCE auth.refresh_tokens_id_seq TO postgres;


--
-- Name: TABLE saml_providers; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.saml_providers TO postgres;
GRANT SELECT ON TABLE auth.saml_providers TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE auth.saml_providers TO dashboard_user;


--
-- Name: TABLE saml_relay_states; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.saml_relay_states TO postgres;
GRANT SELECT ON TABLE auth.saml_relay_states TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE auth.saml_relay_states TO dashboard_user;


--
-- Name: TABLE schema_migrations; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT SELECT ON TABLE auth.schema_migrations TO postgres WITH GRANT OPTION;


--
-- Name: TABLE scim_tokens; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.scim_tokens TO postgres;
GRANT ALL ON TABLE auth.scim_tokens TO dashboard_user;


--
-- Name: TABLE scim_users; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.scim_users TO postgres;
GRANT ALL ON TABLE auth.scim_users TO dashboard_user;


--
-- Name: TABLE sessions; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.sessions TO postgres;
GRANT SELECT ON TABLE auth.sessions TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE auth.sessions TO dashboard_user;


--
-- Name: TABLE sso_domains; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.sso_domains TO postgres;
GRANT SELECT ON TABLE auth.sso_domains TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE auth.sso_domains TO dashboard_user;


--
-- Name: TABLE sso_providers; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.sso_providers TO postgres;
GRANT SELECT ON TABLE auth.sso_providers TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE auth.sso_providers TO dashboard_user;


--
-- Name: TABLE users; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.users TO dashboard_user;
GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.users TO postgres;
GRANT SELECT ON TABLE auth.users TO postgres WITH GRANT OPTION;


--
-- Name: TABLE webauthn_challenges; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.webauthn_challenges TO postgres;
GRANT ALL ON TABLE auth.webauthn_challenges TO dashboard_user;


--
-- Name: TABLE webauthn_credentials; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.webauthn_credentials TO postgres;
GRANT ALL ON TABLE auth.webauthn_credentials TO dashboard_user;


--
-- Name: TABLE pg_stat_statements; Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON TABLE extensions.pg_stat_statements FROM postgres;
GRANT ALL ON TABLE extensions.pg_stat_statements TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE extensions.pg_stat_statements TO dashboard_user;


--
-- Name: TABLE pg_stat_statements_info; Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON TABLE extensions.pg_stat_statements_info FROM postgres;
GRANT ALL ON TABLE extensions.pg_stat_statements_info TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE extensions.pg_stat_statements_info TO dashboard_user;


--
-- Name: TABLE attendance_report_months; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.attendance_report_months TO anon;
GRANT ALL ON TABLE public.attendance_report_months TO authenticated;
GRANT ALL ON TABLE public.attendance_report_months TO service_role;


--
-- Name: SEQUENCE attendance_report_months_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.attendance_report_months_id_seq TO anon;
GRANT ALL ON SEQUENCE public.attendance_report_months_id_seq TO authenticated;
GRANT ALL ON SEQUENCE public.attendance_report_months_id_seq TO service_role;


--
-- Name: TABLE attendance_report_sections; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.attendance_report_sections TO anon;
GRANT ALL ON TABLE public.attendance_report_sections TO authenticated;
GRANT ALL ON TABLE public.attendance_report_sections TO service_role;


--
-- Name: SEQUENCE attendance_report_sections_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.attendance_report_sections_id_seq TO anon;
GRANT ALL ON SEQUENCE public.attendance_report_sections_id_seq TO authenticated;
GRANT ALL ON SEQUENCE public.attendance_report_sections_id_seq TO service_role;


--
-- Name: TABLE audit_logs; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.audit_logs TO anon;
GRANT ALL ON TABLE public.audit_logs TO authenticated;
GRANT ALL ON TABLE public.audit_logs TO service_role;


--
-- Name: SEQUENCE audit_logs_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.audit_logs_id_seq TO anon;
GRANT ALL ON SEQUENCE public.audit_logs_id_seq TO authenticated;
GRANT ALL ON SEQUENCE public.audit_logs_id_seq TO service_role;


--
-- Name: TABLE cache; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.cache TO anon;
GRANT ALL ON TABLE public.cache TO authenticated;
GRANT ALL ON TABLE public.cache TO service_role;


--
-- Name: TABLE cache_locks; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.cache_locks TO anon;
GRANT ALL ON TABLE public.cache_locks TO authenticated;
GRANT ALL ON TABLE public.cache_locks TO service_role;


--
-- Name: TABLE enrollments; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.enrollments TO anon;
GRANT ALL ON TABLE public.enrollments TO authenticated;
GRANT ALL ON TABLE public.enrollments TO service_role;


--
-- Name: SEQUENCE enrollments_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.enrollments_id_seq TO anon;
GRANT ALL ON SEQUENCE public.enrollments_id_seq TO authenticated;
GRANT ALL ON SEQUENCE public.enrollments_id_seq TO service_role;


--
-- Name: TABLE failed_jobs; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.failed_jobs TO anon;
GRANT ALL ON TABLE public.failed_jobs TO authenticated;
GRANT ALL ON TABLE public.failed_jobs TO service_role;


--
-- Name: SEQUENCE failed_jobs_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.failed_jobs_id_seq TO anon;
GRANT ALL ON SEQUENCE public.failed_jobs_id_seq TO authenticated;
GRANT ALL ON SEQUENCE public.failed_jobs_id_seq TO service_role;


--
-- Name: TABLE job_batches; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.job_batches TO anon;
GRANT ALL ON TABLE public.job_batches TO authenticated;
GRANT ALL ON TABLE public.job_batches TO service_role;


--
-- Name: TABLE jobs; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.jobs TO anon;
GRANT ALL ON TABLE public.jobs TO authenticated;
GRANT ALL ON TABLE public.jobs TO service_role;


--
-- Name: SEQUENCE jobs_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.jobs_id_seq TO anon;
GRANT ALL ON SEQUENCE public.jobs_id_seq TO authenticated;
GRANT ALL ON SEQUENCE public.jobs_id_seq TO service_role;


--
-- Name: TABLE meal_plans; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.meal_plans TO anon;
GRANT ALL ON TABLE public.meal_plans TO authenticated;
GRANT ALL ON TABLE public.meal_plans TO service_role;


--
-- Name: SEQUENCE meal_plans_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.meal_plans_id_seq TO anon;
GRANT ALL ON SEQUENCE public.meal_plans_id_seq TO authenticated;
GRANT ALL ON SEQUENCE public.meal_plans_id_seq TO service_role;


--
-- Name: TABLE migrations; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.migrations TO anon;
GRANT ALL ON TABLE public.migrations TO authenticated;
GRANT ALL ON TABLE public.migrations TO service_role;


--
-- Name: SEQUENCE migrations_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.migrations_id_seq TO anon;
GRANT ALL ON SEQUENCE public.migrations_id_seq TO authenticated;
GRANT ALL ON SEQUENCE public.migrations_id_seq TO service_role;


--
-- Name: TABLE nutrition_measurements; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.nutrition_measurements TO anon;
GRANT ALL ON TABLE public.nutrition_measurements TO authenticated;
GRANT ALL ON TABLE public.nutrition_measurements TO service_role;


--
-- Name: SEQUENCE nutrition_measurements_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.nutrition_measurements_id_seq TO anon;
GRANT ALL ON SEQUENCE public.nutrition_measurements_id_seq TO authenticated;
GRANT ALL ON SEQUENCE public.nutrition_measurements_id_seq TO service_role;


--
-- Name: TABLE password_reset_tokens; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.password_reset_tokens TO anon;
GRANT ALL ON TABLE public.password_reset_tokens TO authenticated;
GRANT ALL ON TABLE public.password_reset_tokens TO service_role;


--
-- Name: TABLE report_period_rows; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.report_period_rows TO anon;
GRANT ALL ON TABLE public.report_period_rows TO authenticated;
GRANT ALL ON TABLE public.report_period_rows TO service_role;


--
-- Name: SEQUENCE report_period_rows_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.report_period_rows_id_seq TO anon;
GRANT ALL ON SEQUENCE public.report_period_rows_id_seq TO authenticated;
GRANT ALL ON SEQUENCE public.report_period_rows_id_seq TO service_role;


--
-- Name: TABLE report_periods; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.report_periods TO anon;
GRANT ALL ON TABLE public.report_periods TO authenticated;
GRANT ALL ON TABLE public.report_periods TO service_role;


--
-- Name: SEQUENCE report_periods_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.report_periods_id_seq TO anon;
GRANT ALL ON SEQUENCE public.report_periods_id_seq TO authenticated;
GRANT ALL ON SEQUENCE public.report_periods_id_seq TO service_role;


--
-- Name: TABLE sbfp_parent_approval_requests; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.sbfp_parent_approval_requests TO anon;
GRANT ALL ON TABLE public.sbfp_parent_approval_requests TO authenticated;
GRANT ALL ON TABLE public.sbfp_parent_approval_requests TO service_role;


--
-- Name: SEQUENCE sbfp_parent_approval_requests_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.sbfp_parent_approval_requests_id_seq TO anon;
GRANT ALL ON SEQUENCE public.sbfp_parent_approval_requests_id_seq TO authenticated;
GRANT ALL ON SEQUENCE public.sbfp_parent_approval_requests_id_seq TO service_role;


--
-- Name: TABLE sbfp_participants; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.sbfp_participants TO anon;
GRANT ALL ON TABLE public.sbfp_participants TO authenticated;
GRANT ALL ON TABLE public.sbfp_participants TO service_role;


--
-- Name: SEQUENCE sbfp_participants_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.sbfp_participants_id_seq TO anon;
GRANT ALL ON SEQUENCE public.sbfp_participants_id_seq TO authenticated;
GRANT ALL ON SEQUENCE public.sbfp_participants_id_seq TO service_role;


--
-- Name: TABLE school_year_user_records; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.school_year_user_records TO anon;
GRANT ALL ON TABLE public.school_year_user_records TO authenticated;
GRANT ALL ON TABLE public.school_year_user_records TO service_role;


--
-- Name: SEQUENCE school_year_user_records_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.school_year_user_records_id_seq TO anon;
GRANT ALL ON SEQUENCE public.school_year_user_records_id_seq TO authenticated;
GRANT ALL ON SEQUENCE public.school_year_user_records_id_seq TO service_role;


--
-- Name: TABLE school_years; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.school_years TO anon;
GRANT ALL ON TABLE public.school_years TO authenticated;
GRANT ALL ON TABLE public.school_years TO service_role;


--
-- Name: SEQUENCE school_years_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.school_years_id_seq TO anon;
GRANT ALL ON SEQUENCE public.school_years_id_seq TO authenticated;
GRANT ALL ON SEQUENCE public.school_years_id_seq TO service_role;


--
-- Name: TABLE sessions; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.sessions TO anon;
GRANT ALL ON TABLE public.sessions TO authenticated;
GRANT ALL ON TABLE public.sessions TO service_role;


--
-- Name: TABLE student_attendance_records; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.student_attendance_records TO anon;
GRANT ALL ON TABLE public.student_attendance_records TO authenticated;
GRANT ALL ON TABLE public.student_attendance_records TO service_role;


--
-- Name: SEQUENCE student_attendance_records_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.student_attendance_records_id_seq TO anon;
GRANT ALL ON SEQUENCE public.student_attendance_records_id_seq TO authenticated;
GRANT ALL ON SEQUENCE public.student_attendance_records_id_seq TO service_role;


--
-- Name: TABLE student_feeding_records; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.student_feeding_records TO anon;
GRANT ALL ON TABLE public.student_feeding_records TO authenticated;
GRANT ALL ON TABLE public.student_feeding_records TO service_role;


--
-- Name: SEQUENCE student_feeding_records_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.student_feeding_records_id_seq TO anon;
GRANT ALL ON SEQUENCE public.student_feeding_records_id_seq TO authenticated;
GRANT ALL ON SEQUENCE public.student_feeding_records_id_seq TO service_role;


--
-- Name: TABLE students; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.students TO anon;
GRANT ALL ON TABLE public.students TO authenticated;
GRANT ALL ON TABLE public.students TO service_role;


--
-- Name: SEQUENCE students_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.students_id_seq TO anon;
GRANT ALL ON SEQUENCE public.students_id_seq TO authenticated;
GRANT ALL ON SEQUENCE public.students_id_seq TO service_role;


--
-- Name: TABLE users; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.users TO anon;
GRANT ALL ON TABLE public.users TO authenticated;
GRANT ALL ON TABLE public.users TO service_role;


--
-- Name: SEQUENCE users_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.users_id_seq TO anon;
GRANT ALL ON SEQUENCE public.users_id_seq TO authenticated;
GRANT ALL ON SEQUENCE public.users_id_seq TO service_role;


--
-- Name: TABLE messages; Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE realtime.messages TO postgres;
GRANT SELECT,INSERT ON TABLE realtime.messages TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE realtime.messages TO dashboard_user;
GRANT SELECT,INSERT,UPDATE ON TABLE realtime.messages TO anon;
GRANT SELECT,INSERT,UPDATE ON TABLE realtime.messages TO authenticated;
GRANT SELECT,INSERT,UPDATE ON TABLE realtime.messages TO service_role;


--
-- Name: TABLE subscription; Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON TABLE realtime.subscription TO postgres;
GRANT ALL ON TABLE realtime.subscription TO dashboard_user;
GRANT SELECT ON TABLE realtime.subscription TO anon;
GRANT SELECT ON TABLE realtime.subscription TO authenticated;
GRANT SELECT ON TABLE realtime.subscription TO service_role;


--
-- Name: SEQUENCE subscription_id_seq; Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON SEQUENCE realtime.subscription_id_seq TO postgres;
GRANT ALL ON SEQUENCE realtime.subscription_id_seq TO dashboard_user;
GRANT USAGE ON SEQUENCE realtime.subscription_id_seq TO anon;
GRANT USAGE ON SEQUENCE realtime.subscription_id_seq TO authenticated;
GRANT USAGE ON SEQUENCE realtime.subscription_id_seq TO service_role;


--
-- Name: TABLE buckets; Type: ACL; Schema: storage; Owner: supabase_storage_admin
--

REVOKE ALL ON TABLE storage.buckets FROM supabase_storage_admin;
GRANT ALL ON TABLE storage.buckets TO supabase_storage_admin WITH GRANT OPTION;
GRANT ALL ON TABLE storage.buckets TO service_role;
GRANT ALL ON TABLE storage.buckets TO authenticated;
GRANT ALL ON TABLE storage.buckets TO anon;
GRANT ALL ON TABLE storage.buckets TO postgres WITH GRANT OPTION;


--
-- Name: TABLE buckets_analytics; Type: ACL; Schema: storage; Owner: supabase_storage_admin
--

GRANT ALL ON TABLE storage.buckets_analytics TO service_role;
GRANT ALL ON TABLE storage.buckets_analytics TO authenticated;
GRANT ALL ON TABLE storage.buckets_analytics TO anon;


--
-- Name: TABLE buckets_vectors; Type: ACL; Schema: storage; Owner: supabase_storage_admin
--

GRANT SELECT ON TABLE storage.buckets_vectors TO service_role;
GRANT SELECT ON TABLE storage.buckets_vectors TO authenticated;
GRANT SELECT ON TABLE storage.buckets_vectors TO anon;


--
-- Name: TABLE objects; Type: ACL; Schema: storage; Owner: supabase_storage_admin
--

REVOKE ALL ON TABLE storage.objects FROM supabase_storage_admin;
GRANT ALL ON TABLE storage.objects TO supabase_storage_admin WITH GRANT OPTION;
GRANT ALL ON TABLE storage.objects TO service_role;
GRANT ALL ON TABLE storage.objects TO authenticated;
GRANT ALL ON TABLE storage.objects TO anon;
GRANT ALL ON TABLE storage.objects TO postgres WITH GRANT OPTION;


--
-- Name: TABLE s3_multipart_uploads; Type: ACL; Schema: storage; Owner: supabase_storage_admin
--

GRANT ALL ON TABLE storage.s3_multipart_uploads TO service_role;
GRANT SELECT ON TABLE storage.s3_multipart_uploads TO authenticated;
GRANT SELECT ON TABLE storage.s3_multipart_uploads TO anon;


--
-- Name: TABLE s3_multipart_uploads_parts; Type: ACL; Schema: storage; Owner: supabase_storage_admin
--

GRANT ALL ON TABLE storage.s3_multipart_uploads_parts TO service_role;
GRANT SELECT ON TABLE storage.s3_multipart_uploads_parts TO authenticated;
GRANT SELECT ON TABLE storage.s3_multipart_uploads_parts TO anon;


--
-- Name: TABLE vector_indexes; Type: ACL; Schema: storage; Owner: supabase_storage_admin
--

GRANT SELECT ON TABLE storage.vector_indexes TO service_role;
GRANT SELECT ON TABLE storage.vector_indexes TO authenticated;
GRANT SELECT ON TABLE storage.vector_indexes TO anon;


--
-- Name: TABLE secrets; Type: ACL; Schema: vault; Owner: supabase_admin
--

GRANT SELECT,REFERENCES,DELETE,TRUNCATE ON TABLE vault.secrets TO postgres WITH GRANT OPTION;
GRANT SELECT,DELETE ON TABLE vault.secrets TO service_role;


--
-- Name: TABLE decrypted_secrets; Type: ACL; Schema: vault; Owner: supabase_admin
--

GRANT SELECT,REFERENCES,DELETE,TRUNCATE ON TABLE vault.decrypted_secrets TO postgres WITH GRANT OPTION;
GRANT SELECT,DELETE ON TABLE vault.decrypted_secrets TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR SEQUENCES; Type: DEFAULT ACL; Schema: auth; Owner: supabase_auth_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_auth_admin IN SCHEMA auth GRANT ALL ON SEQUENCES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_auth_admin IN SCHEMA auth GRANT ALL ON SEQUENCES TO dashboard_user;


--
-- Name: DEFAULT PRIVILEGES FOR FUNCTIONS; Type: DEFAULT ACL; Schema: auth; Owner: supabase_auth_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_auth_admin IN SCHEMA auth GRANT ALL ON FUNCTIONS TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_auth_admin IN SCHEMA auth GRANT ALL ON FUNCTIONS TO dashboard_user;


--
-- Name: DEFAULT PRIVILEGES FOR TABLES; Type: DEFAULT ACL; Schema: auth; Owner: supabase_auth_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_auth_admin IN SCHEMA auth GRANT ALL ON TABLES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_auth_admin IN SCHEMA auth GRANT ALL ON TABLES TO dashboard_user;


--
-- Name: DEFAULT PRIVILEGES FOR SEQUENCES; Type: DEFAULT ACL; Schema: extensions; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA extensions GRANT ALL ON SEQUENCES TO postgres WITH GRANT OPTION;


--
-- Name: DEFAULT PRIVILEGES FOR FUNCTIONS; Type: DEFAULT ACL; Schema: extensions; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA extensions GRANT ALL ON FUNCTIONS TO postgres WITH GRANT OPTION;


--
-- Name: DEFAULT PRIVILEGES FOR TABLES; Type: DEFAULT ACL; Schema: extensions; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA extensions GRANT ALL ON TABLES TO postgres WITH GRANT OPTION;


--
-- Name: DEFAULT PRIVILEGES FOR SEQUENCES; Type: DEFAULT ACL; Schema: graphql; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql GRANT ALL ON SEQUENCES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql GRANT ALL ON SEQUENCES TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql GRANT ALL ON SEQUENCES TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql GRANT ALL ON SEQUENCES TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR FUNCTIONS; Type: DEFAULT ACL; Schema: graphql; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql GRANT ALL ON FUNCTIONS TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql GRANT ALL ON FUNCTIONS TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql GRANT ALL ON FUNCTIONS TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql GRANT ALL ON FUNCTIONS TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR TABLES; Type: DEFAULT ACL; Schema: graphql; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql GRANT ALL ON TABLES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql GRANT ALL ON TABLES TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql GRANT ALL ON TABLES TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql GRANT ALL ON TABLES TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR SEQUENCES; Type: DEFAULT ACL; Schema: graphql_public; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql_public GRANT ALL ON SEQUENCES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql_public GRANT ALL ON SEQUENCES TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql_public GRANT ALL ON SEQUENCES TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql_public GRANT ALL ON SEQUENCES TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR FUNCTIONS; Type: DEFAULT ACL; Schema: graphql_public; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql_public GRANT ALL ON FUNCTIONS TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql_public GRANT ALL ON FUNCTIONS TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql_public GRANT ALL ON FUNCTIONS TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql_public GRANT ALL ON FUNCTIONS TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR TABLES; Type: DEFAULT ACL; Schema: graphql_public; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql_public GRANT ALL ON TABLES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql_public GRANT ALL ON TABLES TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql_public GRANT ALL ON TABLES TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql_public GRANT ALL ON TABLES TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR SEQUENCES; Type: DEFAULT ACL; Schema: public; Owner: postgres
--

ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON SEQUENCES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON SEQUENCES TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON SEQUENCES TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON SEQUENCES TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR SEQUENCES; Type: DEFAULT ACL; Schema: public; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA public GRANT ALL ON SEQUENCES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA public GRANT ALL ON SEQUENCES TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA public GRANT ALL ON SEQUENCES TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA public GRANT ALL ON SEQUENCES TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR FUNCTIONS; Type: DEFAULT ACL; Schema: public; Owner: postgres
--

ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON FUNCTIONS TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON FUNCTIONS TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON FUNCTIONS TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON FUNCTIONS TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR FUNCTIONS; Type: DEFAULT ACL; Schema: public; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA public GRANT ALL ON FUNCTIONS TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA public GRANT ALL ON FUNCTIONS TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA public GRANT ALL ON FUNCTIONS TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA public GRANT ALL ON FUNCTIONS TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR TABLES; Type: DEFAULT ACL; Schema: public; Owner: postgres
--

ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON TABLES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON TABLES TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON TABLES TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON TABLES TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR TABLES; Type: DEFAULT ACL; Schema: public; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA public GRANT ALL ON TABLES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA public GRANT ALL ON TABLES TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA public GRANT ALL ON TABLES TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA public GRANT ALL ON TABLES TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR SEQUENCES; Type: DEFAULT ACL; Schema: realtime; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA realtime GRANT ALL ON SEQUENCES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA realtime GRANT ALL ON SEQUENCES TO dashboard_user;


--
-- Name: DEFAULT PRIVILEGES FOR FUNCTIONS; Type: DEFAULT ACL; Schema: realtime; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA realtime GRANT ALL ON FUNCTIONS TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA realtime GRANT ALL ON FUNCTIONS TO dashboard_user;


--
-- Name: DEFAULT PRIVILEGES FOR TABLES; Type: DEFAULT ACL; Schema: realtime; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA realtime GRANT REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA realtime GRANT SELECT,INSERT ON TABLES TO postgres WITH GRANT OPTION;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA realtime GRANT ALL ON TABLES TO dashboard_user;


--
-- Name: DEFAULT PRIVILEGES FOR SEQUENCES; Type: DEFAULT ACL; Schema: storage; Owner: postgres
--

ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA storage GRANT ALL ON SEQUENCES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA storage GRANT ALL ON SEQUENCES TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA storage GRANT ALL ON SEQUENCES TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA storage GRANT ALL ON SEQUENCES TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR FUNCTIONS; Type: DEFAULT ACL; Schema: storage; Owner: postgres
--

ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA storage GRANT ALL ON FUNCTIONS TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA storage GRANT ALL ON FUNCTIONS TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA storage GRANT ALL ON FUNCTIONS TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA storage GRANT ALL ON FUNCTIONS TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR TABLES; Type: DEFAULT ACL; Schema: storage; Owner: postgres
--

ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA storage GRANT ALL ON TABLES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA storage GRANT ALL ON TABLES TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA storage GRANT ALL ON TABLES TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA storage GRANT ALL ON TABLES TO service_role;


--
-- Name: issue_graphql_placeholder; Type: EVENT TRIGGER; Schema: -; Owner: supabase_admin
--

CREATE EVENT TRIGGER issue_graphql_placeholder ON sql_drop
         WHEN TAG IN ('DROP EXTENSION')
   EXECUTE FUNCTION extensions.set_graphql_placeholder();


ALTER EVENT TRIGGER issue_graphql_placeholder OWNER TO supabase_admin;

--
-- Name: issue_pg_cron_access; Type: EVENT TRIGGER; Schema: -; Owner: supabase_admin
--

CREATE EVENT TRIGGER issue_pg_cron_access ON ddl_command_end
         WHEN TAG IN ('CREATE EXTENSION')
   EXECUTE FUNCTION extensions.grant_pg_cron_access();


ALTER EVENT TRIGGER issue_pg_cron_access OWNER TO supabase_admin;

--
-- Name: issue_pg_graphql_access; Type: EVENT TRIGGER; Schema: -; Owner: supabase_admin
--

CREATE EVENT TRIGGER issue_pg_graphql_access ON ddl_command_end
         WHEN TAG IN ('CREATE EXTENSION')
   EXECUTE FUNCTION extensions.grant_pg_graphql_access();


ALTER EVENT TRIGGER issue_pg_graphql_access OWNER TO supabase_admin;

--
-- Name: issue_pg_net_access; Type: EVENT TRIGGER; Schema: -; Owner: supabase_admin
--

CREATE EVENT TRIGGER issue_pg_net_access ON ddl_command_end
         WHEN TAG IN ('CREATE EXTENSION')
   EXECUTE FUNCTION extensions.grant_pg_net_access();


ALTER EVENT TRIGGER issue_pg_net_access OWNER TO supabase_admin;

--
-- Name: pgrst_ddl_watch; Type: EVENT TRIGGER; Schema: -; Owner: supabase_admin
--

CREATE EVENT TRIGGER pgrst_ddl_watch ON ddl_command_end
   EXECUTE FUNCTION extensions.pgrst_ddl_watch();


ALTER EVENT TRIGGER pgrst_ddl_watch OWNER TO supabase_admin;

--
-- Name: pgrst_drop_watch; Type: EVENT TRIGGER; Schema: -; Owner: supabase_admin
--

CREATE EVENT TRIGGER pgrst_drop_watch ON sql_drop
   EXECUTE FUNCTION extensions.pgrst_drop_watch();


ALTER EVENT TRIGGER pgrst_drop_watch OWNER TO supabase_admin;

--
-- PostgreSQL database dump complete
--

\unrestrict WXeOeT3LWrqCXWLoOB9AsC6qR0sKKtsneEc7gApjyxi0uFiAtP5750Dp0nBC0X4

