import { useForm } from '@inertiajs/react';
import { FormEvent } from 'react';
import { Head, Link } from '@inertiajs/react';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/Components/ui/card';
import { Alert, AlertDescription } from '@/Components/ui/alert';
import { PageProps } from '@/types';

interface ForgotPasswordProps extends PageProps {
  status?: string;
}

export default function ForgotPassword({ status }: ForgotPasswordProps) {
  const { data, setData, post, processing, errors } = useForm({
    email: '',
  });

  const submit = (e: FormEvent) => {
    e.preventDefault();
    post(route('company.password.email'));
  };

  return (
    <>
      <Head title="Forgot Password" />

      <div className="min-h-screen flex items-center justify-center bg-gray-50">
        <div className="w-full max-w-md">
          <Card>
            <CardHeader>
              <CardTitle className="text-2xl font-bold text-center">Forgot Password</CardTitle>
              <CardDescription className="text-center">
                No problem. Just let us know your email address and we will email you a password
                reset link that will allow you to choose a new one.
              </CardDescription>
            </CardHeader>

            <CardContent>
              {status && (
                <Alert className="mb-6 bg-green-50 border-green-200">
                  <AlertDescription>{status}</AlertDescription>
                </Alert>
              )}

              <form onSubmit={submit} className="space-y-4">
                <div className="space-y-2">
                  <Label htmlFor="email">Email</Label>
                  <Input
                    id="email"
                    type="email"
                    name="email"
                    value={data.email}
                    className="mt-1 block w-full"
                    onChange={(e) => setData('email', e.target.value)}
                    required
                    autoFocus
                  />
                  {errors.email && <p className="text-sm text-red-600">{errors.email}</p>}
                </div>

                <Button type="submit" className="w-full" disabled={processing}>
                  Email Password Reset Link
                </Button>
              </form>
            </CardContent>

            <CardFooter className="justify-center">
              <Link href={route('company.login')} className="text-sm text-blue-600 hover:underline">
                Back to Login
              </Link>
            </CardFooter>
          </Card>
        </div>
      </div>
    </>
  );
}
