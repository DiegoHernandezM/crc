import React from 'react';
import {InertiaLink, useForm, usePage} from '@inertiajs/inertia-react';
import Layout from '@/Shared/Layout';
import LoadingButton from '@/Shared/LoadingButton';
import TextInput from '@/Shared/TextInput';
import {Box, TextField} from "@material-ui/core";
import Autocomplete from "@material-ui/lab/Autocomplete/Autocomplete";

const Create = () => {

  const { subareas } = usePage().props;
  const { data, setData, errors, post, processing } = useForm({
    quantity: '',
    bono: '',
    subarea_id: '',
  });

  function handleSubmit(e) {
    e.preventDefault();
    post(route('board.store'));
  }

  return (
    <div>
      <h1 className="mb-8 text-3xl font-bold">
        <InertiaLink
          href={route('board')}
          className="text-indigo-600 hover:text-indigo-700"
        >
          Tablero
        </InertiaLink>
        <span className="font-medium text-indigo-600"> /</span> Creación
      </h1>
      <div className="max-w-3xl overflow-hidden bg-white rounded shadow">
        <form onSubmit={handleSubmit}>
          <div className="flex flex-wrap p-8 -mb-8 -mr-6">
            <TextInput
              className="w-full pb-8 pr-6 lg:w-1/2"
              label="Cantidad"
              name="boxes_by_turn"
              errors={errors.quantity}
              value={data.quantity}
              onChange={e => setData('quantity', e.target.value)}
            />
            <TextInput
              className="w-full pb-8 pr-6 lg:w-1/2"
              label="Bono $"
              name="bono"
              errors={errors.bono}
              value={data.bono}
              onChange={e => setData('bono', e.target.value)}
            />
            <Autocomplete
              id="combo-box-ordergroup"
              className="w-full pb-8 pr-6 lg:w-1/2"
              size="small"
              options={subareas}
              renderOption={subareas => (
                <div>
                  <Box>
                    <option value={subareas.id} name={subareas.id} key={subareas.id}>
                      {`${subareas.name}`}
                    </option>
                  </Box>
                </div>
              )}
              noOptionsText="No hay areas registradas"
              getOptionLabel={subareas =>
                `${subareas.name}`
              }
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
          </div>
          <div className="flex items-center justify-end px-8 py-4 bg-gray-100 border-t border-gray-200">
            <LoadingButton
              loading={processing}
              type="submit"
              className="btn-indigo"
            >
              Guardar
            </LoadingButton>
          </div>
        </form>
      </div>
    </div>
  );
};

Create.layout = page => <Layout title="Crear Bono" children={page} />;

export default Create;
