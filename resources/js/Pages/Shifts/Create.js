import React from 'react';
import { InertiaLink, useForm, usePage } from '@inertiajs/inertia-react';
import DateFnsUtils from '@date-io/date-fns';
import moment from "moment";
// components
import Layout from '@/Shared/Layout';
import LoadingButton from '@/Shared/LoadingButton';
import TextInput from '@/Shared/TextInput';
import { MuiPickersUtilsProvider, KeyboardTimePicker } from "@material-ui/pickers";
import Autocomplete from "@material-ui/lab/Autocomplete/Autocomplete";
import { Box, FormControlLabel, Switch, TextField } from "@material-ui/core";

const Create = () => {
  const { areas } = usePage().props;
  const [checkout, setCheckout] = React.useState(null);
  const [checkin, setCheckin] = React.useState(null);
  const { data, setData, errors, post, processing } = useForm({
    name: '',
    checkin: '',
    checkout: '',
    area_id: '',
    extra_hours: false
  });

  const handleChangeCheckin = (date) => {
    setData("checkin", moment(date).format('HH:mm'));
    setCheckin(date);
  };

  const handleChangeCheckout = (date) => {
    setData("checkout", moment(date).format('HH:mm'));
    setCheckout(date);
  };

  const handleChangeExtra = (e) => {
    setData("extra_hours", e.target.checked);
  };

  function handleSubmit(e) {
    e.preventDefault();
    post(route('shifts.store'));
  }

  return (
    <div>
      <h1 className="mb-8 text-3xl font-bold">
        <InertiaLink href={route('shifts')} className="text-indigo-600 hover:text-indigo-700">
          Horarios
        </InertiaLink>
        <span className="font-medium text-indigo-600"> /</span> Creación
      </h1>
      <div className="max-w-3xl overflow-hidden bg-white rounded shadow">
        <form onSubmit={handleSubmit}>
          <div className="flex flex-wrap p-8 -mb-8 -mr-6">
            <TextInput
              className="w-full pb-8 pr-6 lg:w-1/2"
              label="Nombre"
              name="name"
              errors={errors.name}
              value={data.name}
              onChange={e => setData('name', e.target.value)}
            />
            <Autocomplete
              id="combo-box-ordergroup"
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
              getOptionLabel={areas =>
                `${areas.name}`
              }
              autoComplete
              onChange={(event, value) => {
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
            <MuiPickersUtilsProvider utils={DateFnsUtils}>
              <KeyboardTimePicker
                margin="normal"
                required
                id="time-picker"
                label="Entrada"
                name="checkin"
                value={checkin}
                onChange={handleChangeCheckin}
                KeyboardButtonProps={{
                  'aria-label': 'change time',
                }}
              />
              <KeyboardTimePicker
                margin="normal"
                required
                style={{marginLeft: '50px'}}
                id="time-picker"
                label="Salida"
                name="checkout"
                value={checkout}
                onChange={handleChangeCheckout}
                KeyboardButtonProps={{
                  'aria-label': 'change time',
                }}
              />
            </MuiPickersUtilsProvider>
            <FormControlLabel
              control={
                <Switch
                  checked={data.extra_hours}
                  onChange={handleChangeExtra}
                  name="extra_hours"
                  color="primary"
                />
              }
              style={{margin: '30px', marginTop: '30px'}}
              label="Horas Extra"
            />
          </div>
          <div className="flex items-center justify-end px-8 py-4 bg-gray-100 border-t border-gray-200">
            <LoadingButton loading={processing} type="submit" className="btn-indigo">
              Guardar
            </LoadingButton>
          </div>
        </form>
      </div>
    </div>
  );
};

Create.layout = page => <Layout title="Create Organization" children={page} />;
export default Create;
