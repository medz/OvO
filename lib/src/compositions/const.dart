import '../core/context.dart';
import '../core/exception.dart';
import '../core/ovo.dart';

class Const<T> implements OvO<T> {
  final T value;
  final String? message;

  const Const(this.value, [this.message]);

  @override
  Future<T> handle(Context context, data) async {
    if (data == value) return value;

    throw OvOException(
      code: #invalid_value,
      message: message ?? 'Invalid value, expected $value but got $data',
    );
  }
}
