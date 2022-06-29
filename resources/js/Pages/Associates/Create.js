import React from 'react';
// api
import { getSubareaById } from "../../Api/CheckinService/CheckinApi";
// components
import { InertiaLink, useForm, usePage } from '@inertiajs/inertia-react';
import Layout from '@/Shared/Layout';
import LoadingButton from '@/Shared/LoadingButton';
import TextInput from '@/Shared/TextInput';
import Autocomplete from "@material-ui/lab/Autocomplete/Autocomplete";
import { FormLabel, FormControlLabel, Switch, Box, makeStyles, TextField } from "@material-ui/core";
import FileInput from '@/Shared/FileInput';
import Grid from '@material-ui/core/Grid';

const useStyles = makeStyles(theme => ({
  root: {
  },
  media: {
    height: 300,
  },
  formControlTableToolBar: {
    margin: theme.spacing(1),
    marginTop: -theme.spacing(1),
    marginRight: theme.spacing(2),
    minWidth: 160,
    maxWidth: 10,
  },
  textField: {
    marginLeft: theme.spacing(1),
    marginRight: theme.spacing(1),
    width: 200,
  },
}));

const Create = () => {
  const classes = useStyles();
  const { areas, shifts, auth } = usePage().props;
  const [dataSubareas, setDataSubareas] = React.useState([]);
  const { associateTypes } = usePage().props;
  const [state, setState] = React.useState(false);
  const { data, setData, errors, post, processing } = useForm({
    name: '',
    area_id: '',
    subarea_id: '',
    shift_id: '',
    associate_type_id: '',
    employee_number: '',
    entry_date: '',
    picture: '',
    status_id: 1,
    elegible: false,
    user_saalma : '',
    unionized: false,
  });

  function handleSubmit(e) {
    e.preventDefault();
    post(route('associates.store'));
  }

  return (
    <div>
      <h1 className="mb-8 text-3xl font-bold">
        <InertiaLink
          href={route('associates')}
          className="text-indigo-600 hover:text-indigo-700"
        >
          Asociados
        </InertiaLink>
        <span className="font-medium text-indigo-600"> /</span> Agregar
      </h1>
        <Grid container spacing={3}>
          <Grid item xs={6}>
            <div className="max-w-3xl overflow-hidden bg-white rounded shadow">
              <form name="createForm" onSubmit={handleSubmit}>
                <div className="flex flex-wrap p-8 -mb-8 -mr-6">
                  <TextInput
                    className="w-full pb-8 pr-6 lg:w-1/2"
                    required
                    label="Nombre Completo"
                    name="name"
                    errors={errors.name}
                    value={data.name}
                    onChange={e => setData('name', e.target.value)}
                  />
                  <TextInput
                    className="w-full pb-8 pr-6 lg:w-1/2"
                    required
                    label="No. Empleado"
                    name="employeeNumber"
                    errors={errors.employee_number}
                    value={data.employee_number}
                    onChange={e => setData('employee_number', e.target.value)}
                  />
                  <TextInput
                    className="w-full pb-8 pr-6 lg:w-1/2"
                    required
                    id="date-Init"
                    label="Fecha Ingreso"
                    type="date"
                    variant="outlined"
                    onChange={e => setData('entry_date', e.target.value)}
                    defaultValue={data.entry_date}
                  />
                  <TextInput
                    className="w-full pb-8 pr-6 lg:w-1/2"
                    label="Usuario de Saalma"
                    name="user_saalma"
                    errors={errors.user_saalma}
                    value={data.user_saalma}
                    onChange={e => setData('user_saalma', e.target.value)}
                  />
                  <TextInput
                    className="w-full pb-8 pr-6 lg:w-1/2"
                    label="Usuario de Wamas"
                    name="wamas_user"
                    errors={errors.wamas_user}
                    value={data.wamas_user}
                    onChange={e => setData('wamas_user', e.target.value)}
                  />
                  {
                    auth.user.area !== 2 ? <Autocomplete
                      id="combo-box-associate-type"
                      className="w-full pb-8 pr-6 lg:w-1/2"
                      size="small"
                      options={associateTypes}
                      renderOption={associateTypes => (
                        <div>
                          <Box>
                            <option value={associateTypes.id} name={associateTypes.id} key={associateTypes.id}>
                              {`${associateTypes.name}`}
                            </option>
                          </Box>
                        </div>
                      )}
                      noOptionsText="No hay subareas registradas"
                      getOptionLabel={associateTypes =>
                        `${associateTypes.name}`
                      }
                      autoComplete
                      onChange={(event, value) => {
                        setData("associate_type_id",  value?.id);
                      }}
                      renderInput={params => (
                        <TextField
                          {...params}
                          label="Tipo de asociado"
                          variant="outlined"
                          style={{ marginTop: "15px" }}
                          placeholder="Seleccione un tipo de asociado"
                          fullWidth
                        />
                      )}
                      style={{marginTop:'10px'}}
                    /> : null
                  }
                  <Autocomplete
                    id="combo-box-area"
                    className="w-full pb-8 pr-6 lg:w-1/2"
                    size="small"
                    options={areas}
                    renderOption={areas => (
                      <div>
                        <Box>
                          <option value={areas.id} name={areas.id} key={areas.id}>
                              {`${areas.name}`}
                          </option>
                        </Box>
                      </div>
                    )}
                    noOptionsText="No hay areas registradas"
                    getOptionLabel={areas => `${areas.name}`}
                    autoComplete
                    onChange={(event, value) => {
                      getSubareaById(value.id)
                        .then(response => {
                          setDataSubareas(response);
                        })
                        .catch(error => {
                          console.log(error)
                        });
                      setData("area_id",  value?.id);
                    }}
                    renderInput={params => (
                      <TextField
                        {...params}
                        required
                        label="Area"
                        variant="outlined"
                        style={{ marginTop: "15px" }}
                        placeholder="Seleccione un area"
                        fullWidth
                      />
                    )}
                    style={{marginTop:'10px'}}
                  />
                  <Autocomplete
                    id="combo-box-subareas"
                    className="w-full pb-8 pr-6 lg:w-1/2"
                    size="small"
                    options={dataSubareas}
                    renderOption={dataSubareas => (
                      <div>
                        <Box>
                          <option value={dataSubareas.id} name={dataSubareas.id} key={dataSubareas.id}>
                            {`${dataSubareas.name}`}
                          </option>
                        </Box>
                      </div>
                    )}
                    noOptionsText="No hay subareas registradas"
                    getOptionLabel={dataSubareas => `${dataSubareas.name}`}
                    autoComplete
                    onChange={(event, value) => {
                      setData("subarea_id",  value?.id);
                    }}
                    renderInput={params => (
                      <TextField
                        {...params}
                        required
                        label="Subarea"
                        variant="outlined"
                        style={{ marginTop: "15px" }}
                        placeholder="Seleccione una subarea"
                        fullWidth
                      />
                    )}
                    style={{marginTop:'10px'}}
                  />
                  <Autocomplete
                    id="combo-box-shift"
                    className="w-full pb-8 pr-6 lg:w-1/2"
                    size="small"
                    options={shifts}
                    renderOption={shifts => (
                      <div>
                        <Box>
                          <option value={shifts.id} name={shifts.id} key={shifts.id}>
                            {`${shifts.name}`}
                          </option>
                        </Box>
                      </div>
                    )}
                    noOptionsText="No hay horarios registrados"
                    getOptionLabel={shifts => `${shifts.name}`}
                    autoComplete
                    onChange={(event, value) => {
                      setData("shift_id",  value?.id);
                    }}
                    renderInput={params => (
                      <TextField
                        {...params}
                        required
                        label="Horario"
                        variant="outlined"
                        style={{ marginTop: "15px" }}
                        placeholder="Seleccione un horario"
                        fullWidth
                      />
                    )}
                    style={{marginTop:'10px'}}
                  />
                  <FileInput
                    className="w-full pb-8 pr-6 lg:w-1/2"
                    label="Imagen"
                    name="picture"
                    accept="image/*"
                    errors={errors.picture}
                    value={data.picture}
                    onChange={picture => setData('picture', picture)}
                  />
                  <FormControlLabel
                    value={data.unionized}
                    control={
                      <Switch
                        name="enabled"
                        color="primary"
                        checked={state.enabled}
                        onChange={e => setData('unionized', e.target.checked)}
                      />
                    }
                    label="Sindicalizado"
                    labelPlacement="start"
                  />
                </div>
                <div className="flex items-center justify-end px-8 py-4 bg-gray-100 border-t border-gray-200">
                  <LoadingButton loading={processing} type="submit" className="btn-indigo">
                    Guardar Asociado
                  </LoadingButton>
                </div>
              </form>
            </div>
        </Grid>
      </Grid>
    </div>
  );
};

Create.layout = page => <Layout title="Crear Asociado" children={page} />;

export default Create;
