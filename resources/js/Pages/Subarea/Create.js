import React from 'react';
import {InertiaLink, useForm, usePage} from '@inertiajs/inertia-react';
import Layout from '@/Shared/Layout';
import LoadingButton from '@/Shared/LoadingButton';
import TextInput from '@/Shared/TextInput';
import {Box, TextField} from "@material-ui/core";
import Autocomplete from "@material-ui/lab/Autocomplete/Autocomplete";

const Create = () => {
  const { areas } = usePage().props;
  const { data, setData, errors, post, processing } = useForm({
    name: '',
    area_id: '',
  });

  function handleSubmit(e) {
    e.preventDefault();
    post(route('subarea.store'));
  }

  return (
    <div>
      <h1 className="mb-8 text-3xl font-bold">
        <InertiaLink href={route('subarea')} className="text-indigo-600 hover:text-indigo-700">
          Subareas
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

Create.layout = page => <Layout title="Crear Subarea" children={page} />;

export default Create;
