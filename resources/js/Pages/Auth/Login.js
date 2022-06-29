import React from 'react';
import Helmet from 'react-helmet';
import { Box, CardMedia } from '@material-ui/core';
import { Inertia } from '@inertiajs/inertia';
import { useForm } from '@inertiajs/inertia-react';
import Logo from '@/Shared/Logo';
import LoadingButton from '@/Shared/LoadingButton';
import TextInput from '@/Shared/TextInput';
import { makeStyles } from '@material-ui/core/styles';

const useStyles = makeStyles(theme => ({
  title: {
    marginBottom: theme.spacing(2),
  },
  divImage: {
    marginTop: theme.spacing(8),
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
    marginBottom: theme.spacing(2),
  },
  imageCard: {
    width: 140,
    height: 140,
    margin: "0 auto",
    resizeMode: 'center',
    alignSelf: 'center',
  }
}));


export default () => {
  const classes = useStyles();
  const { data, setData, errors, post, processing } = useForm({
    email: '',
    password: '',
    remember: false
  });

  function handleSubmit(e) {
    e.preventDefault();
    post(route('login.attempt'));
  }

  return (
    <div className="flex items-center justify-center min-h-screen p-6 bg-green-700">
      <Helmet title="Login" />
      <div className="w-full max-w-md">        
        <form
          onSubmit={handleSubmit}
          className="mt-8 overflow-hidden bg-white rounded-lg shadow-xl"
        >
          <div className="px-10 py-12">
            <CardMedia className={classes.imageCard} image="/crclogo_small.png" title="CCP Logo" />
            <h1 className="text-3xl font-bold text-center">REPORTES</h1>
            <div className="w-24 mx-auto mt-6 border-b-2" />
            <TextInput
              className="mt-10"
              label="Email"
              name="email"
              type="email"
              errors={errors.email}
              value={data.email}
              onChange={e => setData('email', e.target.value)}
            />
            <TextInput
              className="mt-6"
              label="Contraseña"
              name="password"
              type="password"
              errors={errors.password}
              value={data.password}
              onChange={e => setData('password', e.target.value)}
            />
            <label
              className="flex items-center mt-6 select-none"
              htmlFor="remember"
            >
              <input
                name="remember"
                id="remember"
                className="mr-1"
                type="checkbox"
                checked={data.remember}
                onChange={e => setData('remember', e.target.checked)}
              />
              <span className="text-sm">Recordarme</span>
            </label>
          </div>
          <div className="flex items-center justify-between px-10 py-4 bg-gray-100 border-t border-gray-200">
            <a className="hover:underline" tabIndex="-1" href="#reset-password">

            </a>
            <LoadingButton
              type="submit"
              loading={processing}
              className="btn-indigo"
            >
              Iniciar Sesion
            </LoadingButton>
          </div>
        </form>
      </div>
    </div>
  );
};
