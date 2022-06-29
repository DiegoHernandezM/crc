import React from 'react';
import 'date-fns';
import DateFnsUtils from '@date-io/date-fns';
import Helmet from 'react-helmet';
import moment from "moment";
// components
import { InertiaLink, usePage, useForm } from '@inertiajs/inertia-react';
import Layout from '@/Shared/Layout';
import LoadingButton from '@/Shared/LoadingButton';
import TextInput from '@/Shared/TextInput';
import { MuiPickersUtilsProvider, KeyboardTimePicker } from "@material-ui/pickers";
import { Box, TextField, Switch, FormControlLabel } from "@material-ui/core";
import Autocomplete from "@material-ui/lab/Autocomplete";

const Edit = () => {
  const { shift, areas } = usePage().props;
  const [checkout, setCheckout] = React.useState(moment(shift.checkout , 'HH:mm:ss'));
  const [checkin, setCheckin] = React.useState(moment(shift.checkin , 'HH:mm:ss'));
  const { data, setData, errors, put, processing } = useForm({
    name: shift.name || '',
    checkin: shift.checkin || '',
    checkout: shift.checkout || '',
    area_id: shift.area_id || '',
    extra_hours: shift.extra_hours || false
  });

  function handleSubmit(e) {
    e.preventDefault();
    put(route('shifts.update', shift.id));
  }

  const handleChangeCheckin = (date) => {
    setData("checkin", moment(date).format('HH:mm:ss'));
    setCheckin(date);
  };

  const handleChangeCheckout = (date) => {
    setData("checkout", moment(date).format('HH:mm:ss'));
    setCheckout(date);
  };

  const handleChangeExtra = (e) => {
    setData("extra_hours", e.target.checked);
  };

  function optionStartArea() {
    let selectedArea = null;
    if (areas.length > 0)
      areas.forEach(area => {
        if (area.id === data.area_id) {
          selectedArea = area;
        }
      });
    return selectedArea;
  }

  return (
    <div>
      <Helmet title={data.name} />
      <h1 className="mb-8 text-3xl font-bold">
        <InertiaLink href={route('shifts')} className="text-indigo-600 hover:text-indigo-700">
          Horarios
        </InertiaLink>
        <span className="mx-2 font-medium text-indigo-600">/</span>
        {data.name}
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
                value={optionStartArea()}
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
                    style={{ marginTop: "15px" }}
                    label="Area"
                    variant="outlined"
                    placeholder="Seleccione un area"
                    fullWidth
                  />
                )}
                style={{marginTop:'10px'}}
              />
              <MuiPickersUtilsProvider utils={DateFnsUtils}>
                <KeyboardTimePicker
                  margin="normal"
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
                label="Horas Extra"
                style={{margin: '30px', marginTop: '30px'}}
              />
            </div>
            <div className="flex items-center px-8 py-4 bg-gray-100 border-t border-gray-200">
              <LoadingButton loading={processing} type="submit" className="ml-auto btn-indigo">
                Actualizar
              </LoadingButton>
            </div>
          </form>
        </div>
    </div>
  );
};

Edit.layout = page => <Layout children={page} />;

export default Edit;
