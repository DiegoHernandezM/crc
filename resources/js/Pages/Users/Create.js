import React, { useEffect } from 'react';
import { Inertia } from '@inertiajs/inertia';
import { InertiaLink, useForm, usePage } from '@inertiajs/inertia-react';
import Layout from '@/Shared/Layout';
import LoadingButton from '@/Shared/LoadingButton';
import TextInput from '@/Shared/TextInput';
import SelectInput from '@/Shared/SelectInput';
import FileInput from '@/Shared/FileInput';
import { Box, FormLabel, FormControlLabel, Switch, Typography } from '@material-ui/core';

const Create = () => {
  const { areas, permissions } = usePage().props;
  const { data, setData, errors, post, processing } = useForm({
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    area_id: '',
    photo: '',
    permissions: permissions
  });

  let topics = [];
  let topicKey = 0;

  data.permissions.map(function(k, i) {
    let title = k.name.split(".")[0];
    let label = k.name.split(".")[1] ?? k.name.split(".")[0];

    if (i == 0) {
      topics.push([(<Typography variant="h6" key={`topic${topicKey}`} component="div" gutterBottom>
        {title}
      </Typography>)]);
    }
    else if (i > 0) {
      let prevTitle = data.permissions[i-1].name.split(".")[0];
      if (prevTitle != title) {
        topics.push([(<Typography variant="h6" key={`topic${topicKey}`} component="div" gutterBottom>
          {title}
        </Typography>)]);
        topicKey++;
      }
    }
    topics[topicKey].push((<FormControlLabel
      value={data.permissions[i]}
      key={`switch${i}`}
      control={
        <Switch
          name={`enabled${i}`}
          color="primary"
          checked={data.permissions[i].enabled}
          onChange={e => {
            let newArr = [...data.permissions];
            newArr[i].enabled = e.target.checked;
            setData('permissions', newArr);
          }}
        />
      }
      label={label}
      labelPlacement="start"
    />));
  });

  function handleSubmit(e) {
    e.preventDefault();
    post(route('users.store'));
  }

  return (
    <div>
      <div>
        <h1 className="mb-8 text-3xl font-bold">
          <InertiaLink
            href={route('users')}
            className="text-indigo-600 hover:text-indigo-700"
          >
            Creacion de
          </InertiaLink>
          <span className="font-medium text-indigo-600"> /</span> Usuario
        </h1>
      </div>
      <div className="max-w-3xl overflow-hidden bg-white rounded shadow">
        <form name="createForm" onSubmit={handleSubmit}>
          <div className="flex flex-wrap p-8 -mb-8 -mr-6">
            <TextInput
              className="w-full pb-8 pr-6 lg:w-1/2"
              label="Nombre"
              name="first_name"
              errors={errors.first_name}
              value={data.first_name}
              onChange={e => setData('first_name', e.target.value)}
            />
            <TextInput
              className="w-full pb-8 pr-6 lg:w-1/2"
              label="Apellidos"
              name="last_name"
              errors={errors.last_name}
              value={data.last_name}
              onChange={e => setData('last_name', e.target.value)}
            />
            <TextInput
              className="w-full pb-8 pr-6 lg:w-1/2"
              label="Email"
              name="email"
              type="email"
              errors={errors.email}
              value={data.email}
              onChange={e => setData('email', e.target.value)}
            />
            <TextInput
              className="w-full pb-8 pr-6 lg:w-1/2"
              label="Password"
              name="password"
              type="password"
              errors={errors.password}
              value={data.password}
              onChange={e => setData('password', e.target.value)}
            />
            <SelectInput
              className="w-full pb-8 pr-6 lg:w-1/2"
              label="Area"
              name="area"
              errors={errors.area_id}
              value={data.area_id}
              onChange={e => setData('area_id', e.target.value)}
            >
              {
                areas.map(({ id, name }) => {
                return (
                  <option value={id} key={`area-opt${id}`}>{name}</option>
                );
              })}
            </SelectInput>
            <FileInput
              className="w-full pb-8 pr-6 lg:w-1/2"
              label="Imagen"
              name="photo"
              accept="image/*"
              errors={errors.photo}
              value={data.photo}
              onChange={photo => setData('photo', photo)}
            />
            <Typography variant="h4" style={{ width: '100%' }} align='center' component="div">
              Permisos
            </Typography>
            {topics.map(function(top, i) {
              return (
                <Box key={`topicBox${i}`} sx={{ width: '100%' }}>
                  {top.map(function(elem, x) {
                    return elem;
                  })}
                </Box>
              );
            })}
          </div>
          <div className="flex items-center justify-end px-8 py-4 bg-gray-100 border-t border-gray-200">
            <LoadingButton
              loading={processing}
              type="submit"
              className="btn-indigo"
            >
              Crear Usuario
            </LoadingButton>
          </div>
        </form>
      </div>
    </div>
  );
};

Create.layout = page => <Layout title="Crear Usuario" children={page} />;

export default Create;
