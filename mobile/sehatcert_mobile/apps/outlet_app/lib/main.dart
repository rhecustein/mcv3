import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:hydrated_bloc/hydrated_bloc.dart';
import 'package:path_provider/path_provider.dart';

import 'core/di/injection.dart';
import 'core/router/app_router.dart';
import 'core/theme/app_theme.dart';
import 'features/auth/presentation/bloc/auth_bloc.dart';

void main() async {
  // Ensure Flutter is initialized
  WidgetsFlutterBinding.ensureInitialized();

  // Set preferred orientations
  await SystemChrome.setPreferredOrientations([
    DeviceOrientation.portraitUp,
    DeviceOrientation.portraitDown,
  ]);

  // Initialize HydratedBloc storage
  HydratedBloc.storage = await HydratedStorage.build(
    storageDirectory: await getApplicationDocumentsDirectory(),
  );

  // Configure dependency injection
  await configureDependencies();

  // Run app
  runApp(const OutletApp());
}

/// Main app widget
class OutletApp extends StatelessWidget {
  const OutletApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MultiBlocProvider(
      providers: [
        // Auth BLoC (singleton across the app)
        BlocProvider(
          create: (context) => getIt<AuthBloc>(),
        ),
        // Add more global BLoCs here
      ],
      child: MaterialApp.router(
        title: 'SehatCert Outlet',
        debugShowCheckedModeBanner: false,
        theme: AppTheme.lightTheme,
        darkTheme: AppTheme.darkTheme,
        themeMode: ThemeMode.system,
        routerConfig: AppRouter.router,
        builder: (context, child) {
          return MediaQuery(
            // Prevent text scaling beyond app's design
            data: MediaQuery.of(context).copyWith(
              textScaleFactor: MediaQuery.of(context)
                  .textScaleFactor
                  .clamp(0.8, 1.2),
            ),
            child: child!,
          );
        },
      ),
    );
  }
}
