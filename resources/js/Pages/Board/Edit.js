import React from 'react';
import 'date-fns';
import Helmet from 'react-helmet';
import { Inertia } from '@inertiajs/inertia';
import { InertiaLink, usePage, useForm } from '@inertiajs/inertia-react';
import Layout from '@/Shared/Layout';
import LoadingButton from '@/Shared/LoadingButton';
import TextInput from '@/Shared/TextInput';
import {Box, TextField} from "@material-ui/core";
import Autocomplete from "@material-ui/lab/Autocomplete/Autocomplete";

const Edit = () => {
  const { subareas } = usePage().props;
  const { bono } = usePage().props;
  const { data, setData, errors, put, processing } = useForm({
    quantity: bono.quantity || '',
    bono: bono.bono || '',
    subarea_id: bono.subarea_id || '',
  });

  function handleSubmit(e) {
    e.preventDefault();
    put(route('board.update', bono.id));
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

  return (
    <div>
      <Helmet title={data.name} />
      <h1 className="mb-8 text-3xl font-bold">
        <InertiaLink
          href={route('board')}
          className="text-indigo-600 hover:text-indigo-700"
        >
          Tablero
        </InertiaLink>
        {data.boxes_by_turn}
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
              value={optionStartSubarea()}
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
                  style={{ marginTop: "15px" }}
                  label="Subarea"
                  variant="outlined"
                  placeholder="Seleccione una subarea"
                  fullWidth
                />
              )}
              style={{marginTop:'10px'}}
            />
          </div>
          <div className="flex items-center px-8 py-4 bg-gray-100 border-t border-gray-200">
            <LoadingButton
              loading={processing}
              type="submit"
              className="ml-auto btn-indigo"
            >
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
