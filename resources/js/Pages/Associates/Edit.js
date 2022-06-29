import React, {useEffect} from 'react';
import Helmet from 'react-helmet';
import moment from "moment";
// api
import {getSubareaById} from "../../Api/CheckinService/CheckinApi";
// components
import { Inertia } from '@inertiajs/inertia';
import { InertiaLink, usePage, useForm } from '@inertiajs/inertia-react';
import Layout from '@/Shared/Layout';
import DeleteButton from '@/Shared/DeleteButton';
import LoadingButton from '@/Shared/LoadingButton';
import TextInput from '@/Shared/TextInput';
import Autocomplete from "@material-ui/lab/Autocomplete/Autocomplete";
import { Box, FormControlLabel, Switch, TextField } from "@material-ui/core";
import FileInput from '@/Shared/FileInput';

const Edit = () => {
  const { associate, areas, subareas, shifts, associateTypes, auth } = usePage().props;
  const [dataSubareas, setDataSubareas] = React.useState([]);
  const { data, setData, errors, post, processing } = useForm({
    name: associate.name || '',
    area_id: associate.area_id || '',
    subarea_id: associate.subarea_id || '',
    shift_id: associate.shift_id || '',
    associate_type_id: associate.associate_type_id || '',
    employee_number: associate.employee_number || '',
    entry_date: associate.entry_date || '',
    picture: '',
    status_id: associate.status_id || '',
    elegible: associate.elegible || 0,
    user_saalma: associate.user_saalma || '',
    wamas_user: associate.wamas_user || '',
    unionized: Boolean(associate.unionized) || false,
    _method: 'PUT'
  });

  useEffect(() => {
    getSubareaById(associate.area_id)
      .then(response => {
        setDataSubareas(response);
      })
      .catch(error => {
        console.log(error)
      });
  },[]);

  function handleSubmit(e) {
    e.preventDefault();
    post(route('associates.update', associate.id));
  }

  function destroy() {
    if (confirm('¿Esta de acuerdo en eliminar el asociado?')) {
      Inertia.get(route('associates.destroy', associate.id));
    }
  }

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

  function optionStartSubarea() {
    let selectedSubarea = null;
    if (subareas.length > 0)
    subareas.forEach(subarea => {
      if (subarea.id === data.subarea_id) {
        selectedSubarea = subarea;
      }
    });
    return selectedSubarea;
  }

  function optionStartShift() {
    let selectedShift = null;
    if (shifts.length > 0)
      shifts.forEach(shift => {
      if (shift.id === data.shift_id) {
        selectedShift = shift;
      }
    });
    return selectedShift;
  }

  function optionStartAssociateType() {
    let selectedAssociateType = null;
    if (associateTypes.length > 0)
      associateTypes.forEach(associateType => {
      if (associateType.id === data.associate_type_id) {
        selectedAssociateType = associateType;
      }
    });
    return selectedAssociateType;
  }

  return (
    <div>
      <Helmet title={data.name} />
      <h1 className="mb-8 text-3xl font-bold">
        <InertiaLink href={route('associates')} className="text-indigo-600 hover:text-indigo-700">
          Asociados
        </InertiaLink>
        <span className="mx-2 font-medium text-indigo-600">/</span>
        {data.name}
      </h1>
      <div className="max-w-3xl overflow-hidden bg-white rounded shadow">
        <form onSubmit={handleSubmit}>
          <div className="flex flex-wrap p-8 -mb-8 -mr-6">
            <TextInput
              className="w-full pb-8 pr-6 lg:w-1/2"
              label="Nombre Completo"
              name="name"
              errors={errors.name}
              value={data.name}
              onChange={e => setData('name', e.target.value)}
            />
            <TextInput
              className="w-full pb-8 pr-6 lg:w-1/2"
              label="No. Empleado"
              name="employeeNumber"
              errors={errors.employee_number}
              value={data.employee_number}
              onChange={e => setData('employee_number', e.target.value)}
            />
            <TextInput
              className="w-full pb-8 pr-6 lg:w-1/2"
              id="date-Init"
              label="Fecha Ingreso"
              type="date"
              variant="outlined"
              onChange={e => setData('entry_date', e.target.value)}
              defaultValue={moment(data.entry_date).format("YYYY-MM-DD")}
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
              name="user_saalma"
              errors={errors.wamas_user}
              value={data.wamas_user}
              onChange={e => setData('wamas_user', e.target.value)}
            />
            {
              auth.user.area !== 2 ?  <Autocomplete
                id="combo-box-associate-type"
                className="w-full pb-8 pr-6 lg:w-1/2"
                size="small"
                value={optionStartAssociateType()}
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
                getOptionLabel={associateTypes => `${associateTypes.name}`}
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
              value={optionStartArea()}
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
              value={optionStartSubarea()}
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
              value={optionStartShift()}
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
                  checked={data.unionized}
                  onChange={e => setData('unionized', e.target.checked)}
                />
              }
              label="Sindicalizado"
              labelPlacement="start"
            />
          </div>
          <div className="flex items-center px-8 py-4 bg-gray-100 border-t border-gray-200">
            {!associate.deleted_at && (
              <DeleteButton onDelete={destroy}>
                Elimimar Asociado
              </DeleteButton>
            )}
            <LoadingButton loading={processing} type="submit" className="ml-auto btn-indigo">
              Actualizar Asociado
            </LoadingButton>
          </div>
        </form>
      </div>
    </div>
  );
};

Edit.layout = page => <Layout children={page} />;
export default Edit;
