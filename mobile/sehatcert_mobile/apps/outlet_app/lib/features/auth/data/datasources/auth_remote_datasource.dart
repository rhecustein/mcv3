import 'package:dio/dio.dart';
import 'package:retrofit/retrofit.dart';

import '../models/login_request.dart';
import '../models/login_response.dart';
import '../models/user_model.dart';

part 'auth_remote_datasource.g.dart';

/// Remote data source for authentication
@RestApi()
abstract class AuthRemoteDataSource {
  factory AuthRemoteDataSource(Dio dio, {String baseUrl}) =
      _AuthRemoteDataSource;

  /// Login endpoint
  @POST('/auth/login')
  Future<LoginResponse> login(@Body() LoginRequest request);

  /// Logout endpoint
  @POST('/auth/logout')
  Future<void> logout();

  /// Get current user endpoint
  @GET('/auth/me')
  Future<UserModel> getCurrentUser();

  /// Refresh token endpoint
  @POST('/auth/refresh')
  Future<LoginResponse> refreshToken(
    @Body() Map<String, dynamic> refreshToken,
  );
}
