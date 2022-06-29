import React from 'react';
import Helmet from 'react-helmet';
import { Inertia } from '@inertiajs/inertia';
import { InertiaLink, usePage, useForm } from '@inertiajs/inertia-react';
import Layout from '@/Shared/Layout';
import DeleteButton from '@/Shared/DeleteButton';
import LoadingButton from '@/Shared/LoadingButton';
import TextInput from '@/Shared/TextInput';
import SelectInput from '@/Shared/SelectInput';
import FileInput from '@/Shared/FileInput';
import TrashedMessage from '@/Shared/TrashedMessage';
import { Box, FormLabel, FormControlLabel, Switch, Typography } from '@material-ui/core';

const Edit = () => {
  const { user, areas, permissions } = usePage().props;
  const { data, setData, errors, post, processing } = useForm({
    first_name: user.first_name || '',
    last_name: user.last_name || '',
    email: user.email || '',
    password: user.password || '',
    area_id: user.area_id ?? '1',
    photo: '',
    permissions: permissions,

    // NOTE: When working with Laravel PUT/PATCH requests and FormData
    // you SHOULD send POST request and fake the PUT request like this.
    _method: 'PUT'
  });

  let topics = [];
  let topicKey = 0;
  console.log(data);
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

    // NOTE: We are using POST method here, not PUT/PACH. See comment above.
    post(route('users.update', user.id));
  }

  function destroy() {
    if (confirm('¿Está seguro que desea borrar este usuario?')) {
      Inertia.delete(route('users.destroy', user.id));
    }
  }

  function restore() {
    if (confirm('¿Está seguro que desea recuperar este usuario?')) {
      Inertia.put(route('users.restore', user.id));
    }
  }

  return (
    <div>
      <Helmet title={`${data.first_name} ${data.last_name}`} />
      <div className="flex justify-start max-w-lg mb-8">
        <h1 className="text-3xl font-bold">
          <InertiaLink
            href={route('users')}
            className="text-indigo-600 hover:text-indigo-700"
          >
            Usuarios
          </InertiaLink>
          <span className="mx-2 font-medium text-indigo-600">/</span>
          {data.first_name} {data.last_name}
        </h1>
        {user.photo && (
          <img className="block w-8 h-8 ml-4 rounded-full" src={user.photo} />
        )}
      </div>
      {user.deleted_at && (
        <TrashedMessage onRestore={restore}>
          This user has been deleted.
        </TrashedMessage>
      )}
      <div className="max-w-3xl overflow-hidden bg-white rounded shadow">
        <form onSubmit={handleSubmit}>
          <div className="flex flex-wrap p-8 -mb-8 -mr-6">
            <TextInput
              className="w-full pb-8 pr-6 lg:w-1/2"
              label="Nombre(s)"
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
          <div className="flex items-center px-8 py-4 bg-gray-100 border-t border-gray-200">
            {!user.deleted_at && (
              <DeleteButton onDelete={destroy}>Borrar Usuario</DeleteButton>
            )}
            <LoadingButton
              loading={processing}
              type="submit"
              className="ml-auto btn-indigo"
            >
              Actualizar Usuario
            </LoadingButton>
          </div>
        </form>
      </div>
    </div>
  );
};

Edit.layout = page => <Layout children={page} />;

export default Edit;
