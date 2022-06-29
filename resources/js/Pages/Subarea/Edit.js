import React from 'react';
import 'date-fns';
import Helmet from 'react-helmet';
import { Inertia } from '@inertiajs/inertia';
import { InertiaLink, usePage, useForm } from '@inertiajs/inertia-react';
import Layout from '@/Shared/Layout';
import LoadingButton from '@/Shared/LoadingButton';
import TextInput from '@/Shared/TextInput';
import {TextField, Box} from '@material-ui/core';
import Autocomplete from '@material-ui/lab/Autocomplete';

const Edit = () => {
    const { subarea } = usePage().props;
    const { areas } = usePage().props;
    const { data, setData, errors, put, processing } = useForm({
        name: subarea.name || '',
        area_id: subarea.area_id || '',
    });

    const handleChange = (event) => {
        console.log(event.target.value);
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

    function handleSubmit(e) {
        e.preventDefault();
        put(route('subarea.update', subarea.id));
    }

    return (
        <div>
            <Helmet title={data.name} />
            <h1 className="mb-8 text-3xl font-bold">
                <InertiaLink
                    href={route('subarea')}
                    className="text-indigo-600 hover:text-indigo-700"
                >
                    Subareas
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
                            required
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
